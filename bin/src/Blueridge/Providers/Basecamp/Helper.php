<?php
/**
 * Blueridge
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 */

namespace Blueridge\Providers\Basecamp;

use Blueridge\Providers\Basecamp\BasecampClient as Service;
use Blueridge\Documents\User;

class Helper
{
    /**
     * Basecamp Client Service
     * @var Guzzle\HTTP\Client
     */
    private $service;

    /**
     * Basecamp Helper
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * Return all the users projects
     * @param  User   $user
     * @return Array
     */
    public function fetchTodos(User $user, $projectIds = null)
    {
        $projectIds = (empty($projectIds))?$user->profile['projects']:$projectIds;
        $todos = [];
        $todolists = $this->fetchTodolists($user, $projectIds);
        if (empty($todolists)) {
            return;
        }

        $todolistIterator = new \ArrayIterator($todolists);

        foreach ($todolistIterator as $todolist) {

            $basecampTodolist = $this->service->getTodolist([
                'accountId'=>$todolist['rel']['project']['account']['id'],
                'projectId'=>$todolist['rel']['project']['id'],
                'todolistId'=>$todolist['id']
                ]);
            $list = $basecampTodolist['todos']['remaining'];
            $rel=['project'=>$todolist['rel']['project'],'list_name'=>$basecampTodolist['name']];

            array_walk($list, function(&$a, $key, $rel) {
                $a['rel'] = $rel;
                $a['rel']['href'] = $this->getSiteUrl($a['url']);
            },$rel);

            $todos = array_merge($todos,$list);
        }
        return $todos;
    }

    /**
     * Fetch all todolists that can be accessed by a user
     * @param  User   $user
     * @param  Array $selectedProjectIds
     * @return Array
     */
    public function fetchTodolists(User $user, Array $selectedProjectIds)
    {

        $todolists = [];
        $projectIterator = new \ArrayIterator($user->projects);

        foreach ($projectIterator as $project) {
            if(in_array($project['id'], $selectedProjectIds)){

                $todolist = $this->service->getTodolistsByProject([
                    'accountId'=>$project['account']['id'],
                    'projectId'=>$project['id']
                    ]);

                if(!empty($todolist)) {
                    array_walk($todolist, function(&$a, $key, $project) {
                        $a['rel']['project'] = $project;
                    },$project);
                    $todolists = array_merge($todolists,$todolist);
                }
            }
        }

        return $todolists;
    }

    /**
     * Set up the for storage
     * @param  Array  $todoItem
     * @return Array
     */
    public function toStoreage(Array $todoItem)
    {
        return [
        'todoId'=> $todoItem['rel']['project']['account']['product'].'_'.$todoItem['id'],
        'title' => $todoItem['content'],
        'assignee' => $this->setAssignee($todoItem),
        'dueOn' => $todoItem['due_on'],
        'dueDate' => $this->getDueDate($todoItem['due_on']),
        'overdueBy' => $this->calculateOverdueDays($todoItem['due_on']),
        'source' => $this->service->getTodo([
            'accountId'=>$todoItem['rel']['project']['account']['id'],
            'projectId'=>$todoItem['rel']['project']['id'],
            'todoId'=>$todoItem['id'],
            ]),
        'rel' => $todoItem['rel']
        ];

    }

    /**
     * Return an assignee
     * @param Array $todoItem
     */
    protected function setAssignee(Array $todoItem)
    {
        if (empty($todoItem['assignee'])) {
            return ['id'=>null,'type'=>'Person','name'=>'Unassigned'];
        }

        return $todoItem['assignee'];
    }

    /**
     * Reteun a default due date
     * @param  string $due_on
     * @return Timestamp
     */
    protected function getDueDate($due_on)
    {
        if(empty($due_on)) {

            $now= new \DateTime('now');
            return $now->add(new \DateInterval('P6Y'))->getTimestamp();
        }

        $due_on= new \DateTime($due_on);
        return $due_on->getTimestamp();
    }

    /**
     * Calculate overdue days
     * @param string $due_on
     * @return int
     */
    protected function calculateOverdueDays($due_on)
    {
        if(!empty($due_on)) {
            $due_on= new \DateTime($due_on);
            $now= new \DateTime('now');

            if($now > $due_on){
                return $due_on->diff($now, true)->format('%a');
            }
        }
        return 0;
    }

    /**
     * Return a href from a  filtered api endpoint
     * @param  Url $url
     * @return Url
     */
    protected function getSiteUrl($url){
        $points = ['/api/v1','.json'];
        $siteUrl = str_replace($points,'',$url);
        return $siteUrl;
    }
}