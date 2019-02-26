<?php

namespace TheWebmen\FacetFilters\Services;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\ORM\DataObject;
use Werkenbij\JobPage;

class ElasticaService
{
    use Extensible;
    use Injectable;
    use Configurable;


    /**
     * @var \Elastica\Index
     */
    protected $index;

    public function __construct()
    {
        $client = new \Elastica\Client();
        $this->index = $client->getIndex(self::config()->get('index_name'));
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function add($record)
    {
        $type = $this->index->getType($record->getElasticaType());
        $type->addDocument($record->getElasticaDocument());
    }

    public function delete($record)
    {
        $type = $this->index->getType($record->getElasticaType());
        $type->deleteDocument($record->getElasticaDocument());
    }

    public function reindex()
    {
        $this->index->delete();
        $this->index->create();

        $documents = [];

        foreach ($this->getIndexedClasses() as $class) {
            $instance = $class::singleton();
            $type = $this->index->getType($instance->getElasticaType());

            $mapping = $instance->getElasticaMapping();
            $mapping->setType($type);
            $mapping->send();

            if (class_exists('Translatable')) {
                foreach (Translatable::get_allowed_locales() as $locale) {
                    Translatable::set_current_locale($locale);

                    foreach ($class::get() as $record) {
                        $documents[] = $record->getElasticaDocument();
                    }
                }
            } else {
                foreach ($class::get() as $record) {
                    $documents[] = $record->getElasticaDocument();
                }
            }
        }

        $this->index->addDocuments($documents);
    }

    public function search(\Elastica\Query $query)
    {
        return $this->index->search($query);
    }

    public function getIndexedClasses()
    {
        $classes = [];
        foreach (ClassInfo::subclassesFor(DataObject::class) as $candidate) {
            if (singleton($candidate)->hasExtension(\TheWebmen\FacetFilters\Extensions\FilterIndexItemExtension::class)) {
                $classes[] = $candidate;
            }
        }
        return $classes;
    }

}
