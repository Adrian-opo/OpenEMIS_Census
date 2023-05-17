<?php
echo $this->Html->css('OpenEmis.../plugins/progressbar/css/bootstrap-progressbar-3.3.0.min', ['block' => true]);
echo $this->Html->script('OpenEmis.../plugins/progressbar/bootstrap-progressbar.min', ['block' => true]);
echo $this->Html->script('Report.report.list', ['block' => true]);

$this->extend('OpenEmis./Layout/Panel');
$this->start('toolbar');
	foreach ($toolbarButtons as $key => $btn) {
		if (!array_key_exists('type', $btn) || $btn['type'] == 'button') {
			echo $this->Html->link($btn['label'], $btn['url'], $btn['attr']);
		} else if ($btn['type'] == 'element') {
			echo $this->element($btn['element'], $btn['data'], $btn['options']);
		}
	}
$this->end();

$this->start('panelBody');
	$tableHeaders = [
		__('Name'),
		__('Started On'),
		__('Generated By'),
		__('Completed On'),
		__('Expires On'),
		[__('Status') => ['style' => 'width: 150px']],
		__('Action')
	];

	$params = $this->request->params;
	$url = ['plugin' => $params['plugin'], 'controller' => $params['controller'], 'action' => 'ajaxGetReportProgress'];
	$url = $this->Url->build($url);
	$table = $ControllerAction['table'];
	$downloadText = __('Downloading...');
	
?>

<style type="text/css">
.none { display: none !important; }
</style>
<div class="table-wrapper">
	<div class="table-responsive">
		<table class="table table-curved" id="ReportList" url="<?= $url ?>" data-downloadtext="<?= $downloadText ?>">
			<thead><?= $this->Html->tableHeaders($tableHeaders) ?></thead>
			<tbody>
			
				<?php foreach ($data as $obj) : 
					 $fileFormat = json_decode($obj->params);
				 ?>
				<tr row-id="<?= $obj->id ?>">
					<td><?= $obj->name ?></td>
                    <td><?= $table->formatDateTime($obj->created) ?></td>
                    <td><?= $obj->has('created_user') ? $obj->created_user->name_with_id : '' ?></td>
					<td class="modified"><?= !empty($obj->file_path) ? $table->formatDateTime($obj->modified) : '' ?></td>
					<td class="expiryDate"><?= $table->formatDateTime($obj->expiry_date) ?></td>
					<td>
						<?php
						$downloadClass = 'download';
						$errorClass = 'none';
						$status = $obj->status;

						if ($status == 1 && empty($obj->file_path)) {
							$downloadClass = 'download none';
							$progress = 0;
							$current = $obj->current_records;
							$total = $obj->total_records;

							if ($current > 0 && $total > 0) {
								$progress = intval($current / $total * 100);
							}
						
							if ($params['action'] == 'CustomReports') {
								echo __('In Progress');
							} else {
								echo __('In Progress');						
								// echo '<div class="progress progress-striped active" style="margin-bottom:0">';
								// echo '<div class="progress-bar progress-bar-striped" role="progressbar" data-transitiongoal="' . $progress . '" data-status="' . $status . '"></div>';
								// echo '</div>';
							}
						} else if ($status == -1) {
							$downloadClass = 'none';
							$errorClass = '';
						}	else if ($status == 0 && !empty($obj->file_path)) {
							echo __('Completed');	
						}
						// $downloadOptions = ($fileFormat->format == 'zip')?'zipArchievePhoto':'download';
						// echo $this->Html->link(__('Download'), ['action' => $ControllerAction['table']->alias(), $downloadOptions, $obj->id, $ControllerAction['table']->alias()], ['class' => $downloadClass, 'target' => '_self'], []);
						?>
						<a href="#" data-toggle="tooltip" title="<?= __('Please contact the administrator for assistance.') ?>" class="<?php echo $errorClass ?>"><?php echo __('Error') ?></a>
					</td>
					 <td class="rowlink-skip">
					 <?php if ($status == 0 && !empty($obj->file_path)) {?>	
						<div class="dropdown">
							<button class="btn btn-dropdown action-toggle" type="button" id="action-menu" data-toggle="dropdown" aria-expanded="false">
							Select<span class="caret-down"></span>
							</button>
						 <?php
							$downloadUrl = ['plugin' => $params['plugin'],
								'controller' => $params['controller'],
								'action' =>  $ControllerAction['table']->alias(),
								'download',$obj->id
							];
							if ($obj->module == 'InstitutionStatistics') {
                                $viewUrl = ['plugin' => 'Report',
                                    'controller' => 'Reports',
                                    'action' => 'ViewReport',
                                    'report_process_id' => $obj->id,
                                    'file_path' => $obj->file_path,
                                    'module' => $obj->module,
                                ];
                            }
                            else{
                                $viewUrl = ['plugin' => $params['plugin'],
                                    'controller' => $params['controller'],
                                    'action' => 'ViewReport',
                                    'report_process_id' => $obj->id,
                                    'file_path' => $obj->file_path,
                                    'module' => $obj->module,
                                ];
                            }
							$deleteUrl = ['plugin' => $params['plugin'],
								'controller' => $params['controller'],
								'action' =>  $ControllerAction['table']->alias(),
								'removeReport',$obj->id
							];
						?>	
											
						<ul class="dropdown-menu action-dropdown" role="menu" aria-labelledby="action-menu">
							<div class="dropdown-arrow"><i class="fa fa-caret-up"></i></div>
								<li role="presentation">
									<a href="<?php echo $this->Url->build($viewUrl); ?>" role="menuitem" tabindex="-1"><i class="fa fa-eye"></i>View</a>			
								</li>
								<li role="presentation">
									<a href="<?php echo $this->Url->build($downloadUrl); ?>" role="menuitem" tabindex="-1" target ="_self"><i class="fa fa-download"></i>Download</a>
								</li>
								<?php if ($UsersCheck['super_admin'] == 1) { ?>
									<li role="presentation">
										<a href="<?php echo $this->Url->build($deleteUrl); ?>" role="menuitem" tabindex="-1" target ="_self"><i class="fa fa-trash"></i>Delete</a>	
								    </li>
								<?php }?>
								<?php if (!empty($AccessCheck) && $AccessCheck == 1) {?>
									<li role="presentation">
										<a href="<?php echo $this->Url->build($deleteUrl); ?>" role="menuitem" tabindex="-1" target ="_self"><i class="fa fa-trash"></i>Delete</a>	
								    </li>
								<?php }?>		
						    </ul>						
						</div>
						<?php } else if (!empty($AccessCheck) && $AccessCheck == 1 || $UsersCheck['super_admin'] == 1) {?>
							<div class="dropdown">
								<button class="btn btn-dropdown action-toggle" type="button" id="action-menu" data-toggle="dropdown" aria-expanded="false">Select<span class="caret-down"></span></button>
								<?php
									$deleteUrl = ['plugin' => $params['plugin'],
										'controller' => $params['controller'],
										'action' =>  $ControllerAction['table']->alias(),
										'removeReport',$obj->id
									];
								?>
								<ul class="dropdown-menu action-dropdown" role="menu" aria-labelledby="action-menu">
									<div class="dropdown-arrow"><i class="fa fa-caret-up"></i></div>
									<li role="presentation">
										<a href="<?php echo $this->Url->build($deleteUrl); ?>" role="menuitem" tabindex="-1" target ="_self"><i class="fa fa-trash"></i>Delete</a>
									</li>
								</ul>
							</div>
						<?php } ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<?php
$this->end();
