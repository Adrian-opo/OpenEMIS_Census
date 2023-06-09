<div class="toolbar-responsive panel-toolbar">
	<div class="toolbar-wrapper">
		<?php
			$baseUrl = $this->Url->build([
					'plugin' => $this->request->params['plugin'],
				    'controller' => $this->request->params['controller'],
				    'action' => 'StudentAbsences',
				]);

			if (!empty($academicPeriodList)) {
				echo $this->Form->input('academic_period_', array(
					'class' => 'form-control',
					'label' => false,
					'options' => $academicPeriodList,
					'url' => $baseUrl,
					'default' => $selectedAcademicPeriod,
					'data-named-key' => 'academic_period',
				));
			}

			if (!empty($monthOptions)) {
				echo $this->Form->input('month', array(
					'class' => 'form-control',
					'label' => false,
					'options' => $monthOptions,
					'url' => $baseUrl,
					'default' => $selectedMonth,
					'data-named-key' => 'month',
				));
			}

			if (!empty($dateFromOptions)) {
				echo $this->Form->input('academic_period_', array(
					'class' => 'form-control',
					'label' => false,
					'options' => $dateFromOptions,
					'url' => $baseUrl,
					'default' => $selectedDateFrom,
					'data-named-key' => 'dateFrom',
					'data-named-group' => 'academic_period',
				));
			}

			if (!empty($dateToOptions)) {
				echo $this->Form->input('academic_period_', array(
					'class' => 'form-control',
					'label' => false,
					'options' => $dateToOptions,
					'url' => $baseUrl,
					'default' => $selectedDateTo,
					'data-named-key' => 'dateTo',
					'data-named-group' => 'academic_period,dateFrom',
				));
			}

		?>
	</div>
</div>
