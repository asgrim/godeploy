<?php

/**
 * GoDeploy deployment application
 * Copyright (C) 2011 James Titcumb, Simon Wade
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright 2011 GoDeploy
 * @author James Titcumb, Simon Wade, Jon Wigham
 * @link http://www.godeploy.com/
 */
class HistoryController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {        
        $slug = $this->_request->getParam('project');
            // get project slug to identify project
        
        $auth = Zend_Auth::getInstance();
        
        if($auth->hasIdentity())
        {
            $this->view->deployments = $this->getDeploymentsBySlug($slug,$auth->getIdentity());
                // get history of deployments for this project
        }
        
        $this->view->project = $slug;
        
        $this->view->headLink()->appendStylesheet("/css/pages/history.css");
    }
    
    /**
     * Get deployments relating to a project by cross-referencing username with project's slug
     * @param string $slug
     * @param string $username
     * @return array
     */        
    private function getDeploymentsBySlug($slug,$username)
    {
        $projectsTable = new GD_Model_DbTable_Projects();
                
        $usersTable = new GD_Model_DbTable_Users();
        
        $deploymentsTable = new GD_Model_DbTable_Deployments();
        $deploymentsTableInfo = $deploymentsTable->info();
        
        $deploymentStatusesTable = new GD_Model_DbTable_DeploymentFileStatuses();
        $deploymentStatusesInfo = $deploymentStatusesTable->info();
        
        $serversTable = new GD_Model_DbTable_Servers();
        $serversTableInfo = $serversTable->info();
        
        $connectionTypes = new GD_Model_DbTable_ConnectionTypes();
        $connectionTypesInfo = $connectionTypes->info();
        
        $nestedJoin = $serversTable
            ->select()
            ->setIntegrityCheck(false)
            ->from(array('nestedServers' => $serversTableInfo['name']))
            ->joinLeft(
                array('nestedConnectionTypes' => $connectionTypesInfo['name']),
                'nestedConnectionTypes.id = nestedServers.connection_types_id',
                'name AS connection_type'     
            );
        
        $select = $deploymentsTable
            ->select()
            ->setIntegrityCheck(false)
            ->from(
                array('t1' => $deploymentsTableInfo['name']),
                array('id','when','from_revision','to_revision')
                )                
            ->joinLeft(
                array('t2' => $deploymentStatusesInfo['name']),
                't2.id = t1.deployment_statuses_id',
                array('name AS deployment_status')
                )
            ->joinLeft(
                array('t3' => new Zend_Db_Expr("({$nestedJoin})")),
                't3.id = t1.servers_id',
                array('connection_type','name AS server_name','hostname','port','remote_path')
                )    
            ->where('t1.projects_id IN ?',$projectsTable
                ->select()
                ->from($projectsTable,array('id'))
                ->where('slug = ?',(string)$slug)
                )
            ->where('t1.users_id IN ?',$usersTable
                ->select()
                ->from($usersTable,array('id'))
                ->where('name = ?',(string)$username)
                )
            ->order('t1.id ASC');

        $rows = $deploymentsTable->fetchAll($select)->toArray();

        // Append all related file information to the deployments:- 
        
        $deploymentFiles = new GD_Model_DbTable_DeploymentFiles();
        $deploymentFilesInfo = $deploymentFiles->info();
        
        $deploymentFileStatuses = new GD_Model_DbTable_DeploymentFileStatuses();
        $deploymentFileStatusesInfo = $deploymentFileStatuses->info();
        
        $deploymentFileActions = new GD_Model_DbTable_DeploymentFileActions();
        $deploymentFileActionsInfo = $deploymentFileActions->info();
        
        foreach($rows as $key => $value) 
        {
            $files_select = $deploymentFiles
                ->select()
                ->setIntegrityCheck(false)
                ->from(
                    array('t1' => $deploymentFilesInfo['name']),
                    array('id','details')
                    )
                ->joinLeft(
                    array('t2' => $deploymentFileActionsInfo['name']),
                    't1.deployment_file_actions_id = t2.id',    
                    array('name AS file_action')    
                    )
                ->joinLeft(
                    array('t3' => $deploymentFileStatusesInfo['name']),
                    't1.deployment_file_statuses_id = t3.id',
                    array('name AS file_status')
                    )
                ->where('deployments_id = ?',(int)$rows[$key]['id'])
                ->order('t1.id ASC');
            
            $rows[$key]['files'] = $deploymentsTable->fetchAll($files_select)->toArray();
        }
        
        if(is_null($rows))
        {
            return array();
        }        
        return $rows;
    }
}

