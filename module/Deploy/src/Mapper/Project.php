<?php

namespace Deploy\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;

class Project extends AbstractDbMapper
{
    protected $tableName  = 'project';

    /**
     * Fetch all the projects
     *
     * @return \Deploy\Entity\Project[]
     */
    public function findAll()
    {
        $select = $this->getSelect();

        $projects = [];
        foreach ($this->select($select) as $project) {
            $projects[$project->getName()] = $project;
        }

        return $projects;
    }

    /**
     * Find a single project by it's "short" name
     *
     * @param  string                 $name
     * @return \Deploy\Entity\Project
     */
    public function findByName($name)
    {
        $select = $this->getSelect()->where(['name' => $name]);
        $project = $this->select($select)->current();

        return $project;
    }

    /**
     * Find a Project by it's ID
     *
     * @param  int                    $id
     * @return \Deploy\Entity\Project
     */
    public function findById($id)
    {
        $select = $this->getSelect()->where(['id' => $id]);
        $project = $this->select($select)->current();

        return $project;
    }
}
