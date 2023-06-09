<?php
namespace Report\Model\Table;

use ArrayObject;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\Event\Event;
use App\Model\Table\AppTable;
use App\Model\Traits\OptionsTrait;
use Cake\Network\Request;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

class WorkflowInstitutionPositionTable extends AppTable
{
    use OptionsTrait;

    public function initialize(array $config) 
    {
        $this->table("institution_positions");
        parent::initialize($config);

        $this->belongsTo('Statuses', ['className' => 'Workflow.WorkflowSteps', 'foreignKey' => 'status_id']);
        $this->belongsTo('StaffPositionTitles', ['className' => 'Institution.StaffPositionTitles']);
        $this->belongsTo('StaffPositionGrades', ['className' => 'Institution.StaffPositionGrades']);
        $this->belongsTo('Institutions', ['className' => 'Institution.Institutions']);
        $this->belongsTo('Assignees', ['className' => 'User.Users']);

        $this->hasMany('InstitutionStaff', ['className' => 'Institution.Staff', 'dependent' => true, 'cascadeCallbacks' => true]);
        $this->hasMany('StaffPositions', ['className' => 'Staff.Positions', 'dependent' => true, 'cascadeCallbacks' => true]);
        $this->hasMany('StaffTransferIn', ['className' => 'Institution.StaffTransferIn', 'foreignKey' => 'new_institution_position_id', 'dependent' => true, 'cascadeCallbacks' => true]);

        $this->addBehavior('Report.ReportList');
        $this->addBehavior('Report.WorkflowReport');
        $this->addBehavior('Excel', [
            'pages' => false,
            'autoFields' => false
        ]);
    }

    public function onExcelGetIsHomeroom(Event $event, Entity $entity)
    {
        $institutionStaff = TableRegistry::get('institution_staff');
        $institutionPositions = TableRegistry::get('institution_positions');
        
        $options = $this->getSelectOptions('general.yesno');
        $homeroomData =  $institutionPositions->find('all')
                         ->select(['is_homeroom'=> $institutionStaff->aliasField('is_homeroom')])
                         ->leftJoin([$institutionStaff->alias() => $institutionStaff->table()],
                            [$institutionStaff->aliasField('institution_position_id = ') . $this->aliasField('id')])
                         ->first();

        if (!empty($homeroomData)) {
            return $options[$entity->is_homeroom];
        }else{
             return '';
        }
    }
}
