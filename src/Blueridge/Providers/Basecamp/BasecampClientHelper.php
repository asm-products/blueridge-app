<?php
/**
 * Basecamp Client Helper
 */

namespace BlueRidge\Providers\Basecamp;

class BasecampClientHelper
{
    /**
     * Get Accounts
     */
    public static function getAccounts()
    {
        $accounts=array_column($this->accounts,'name');
        return ['basecamp'=>$accounts];
    }


    /**
     * Organise Todos
     */
    public static function organizeTodos($todoItems)
    {
        if(empty($todoItems))
        {
            return;
        }

        $todos = [];
        $todosIterator = new \RecursiveArrayIterator($todoItems);

        foreach($todosIterator as $key => $todo){
            $initPos = 0;
            $todo['overdue_by'] =null; 

            if(!empty($todo['due_on'])){
                $initPos = 1;
                $dueOn= new \DateTime($todo['due_on']);
                $todo['due_on']=$dueOn->format('m/d/Y');
                $todo['overdue_by'] = (int) self::getOverdueBy($dueOn);
            }
            // set initial order
            $due_on[$key] = $todo['due_on'];
            $position[$key] = $initPos;
            $overdue_by[$key] = $todo['overdue_by'];
            $todos[]=$todo;
        }
        array_multisort($overdue_by,SORT_DESC,$position,SORT_DESC,$due_on,SORT_ASC,$todos);
        return $todos;  

    }

    /**
     * Get Over Due By
     */
    public static function getOverdueBy($dueDate)
    {

        $now = new \DateTime('now');

        if($dueDate > $now){
            return 0;
        }

        $interval = $dueDate->diff($now);
        return $interval->format('%a');

    }


    /**
     * Get Site Url
     */
    public static function getSiteUrl($url){
        $points = ['/api/v1','.json'];
        $siteUrl = str_replace($points,'',$url);
        return $siteUrl;
    }
}