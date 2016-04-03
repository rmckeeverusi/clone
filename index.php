<!DOCTYPE html>
<html> 
	<head> 
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="Content-Script-Type" content="text/javascript; charset=utf-8">
			
			<link rel="stylesheet" type="text/css" href="styles/site.css">
			<script src="js/jquery.min.js"></script>
			<script src="js/kendo.all.min.js"></script>
			<link rel="stylesheet" href="styles/kendo.common.min.css" />
			<link rel="stylesheet" href="styles/kendo.default.min.css" />
			
			
			
	</head>
	<body> 
		<table id="t01">
				
				<tr>
					<td>Network</td>
				</tr>
				
				<tr>
					<td>
					<div id="tabs">
					<nav>
						<ul>
						<li><a href="#tabs-1">Map</a></li>
						<li id="selected"><a href="#tabs-2">Maintances Tracker</a></li>
						<li><a href="#tabs-3">History</a></li>
						</ul>
					</nav>
					
					</div>
					</td>
					
				</tr>
				<tr>
					<td>Location</td>
					
				</tr>
		</table>
		<div id="grid"></div> 
		<script>
				$(function() {
					$("#grid").kendoGrid({
						dataSource: {
							transport: {
								read: "data/devices.php"
							},
							schema: {
								data: "data"
							}
						},
						columns: 
							[{ field: "ticketID" },
							{ field: "component" }, 							
							{ field: "device" }, 
							{ field: "severity" },
							{ field: "type" },
							{ field: "owner" },
							{ field: "created" },
							{ field: "updated" },
							{ field: "list" }
							]
							
					});
				});
				
			</script>
			
			<?php
							require_once '/lib/DataSourceResult.php';
							require_once '/lib/Kendo/Autoload.php';

							if ($_SERVER['REQUEST_METHOD'] == 'POST') {
								header('Content-Type: application/json');

								$request = json_decode(file_get_contents('php://input'));

								$result = new DataSourceResult('data/devices.php');

								$type = $_GET['type'];

								$columns = array('ticketID', 'component', 'device', 'severity', 'type');

								switch($type) {
									case 'create':
										$result = $result->create('ticketID', $columns, $request->models, 'ticketID');
										break;
									case 'read':
										$result = $result->read('ticketID', $columns, $request);
										break;
									case 'update':
										$result = $result->update('ticketID', $columns, $request->models, 'ticketID');
										break;
									case 'destroy':
										$result = $result->destroy('ticketID', $request->models, 'ticketID');
										break;
								}

								echo json_encode($result, JSON_NUMERIC_CHECK);

								exit;
							}

							$transport = new \Kendo\Data\DataSourceTransport();

							$create = new \Kendo\Data\DataSourceTransportCreate();

							$create->url('indexGrid.php?type=create')
								 ->contentType('application/json')
								 ->type('POST');

							$read = new \Kendo\Data\DataSourceTransportRead();

							$read->url('indexGrid.php?type=read')
								 ->contentType('application/json')
								 ->type('POST');

							$update = new \Kendo\Data\DataSourceTransportUpdate();

							$update->url('indexGrid.php?type=update')
								 ->contentType('application/json')
								 ->type('POST');

							$destroy = new \Kendo\Data\DataSourceTransportDestroy();

							$destroy->url('indexGrid.php?type=destroy')
								 ->contentType('application/json')
								 ->type('POST');

							$transport->create($create)
									  ->read($read)
									  ->update($update)
									  ->destroy($destroy)
									  ->parameterMap('function(data) {
										  return kendo.stringify(data);
									  }');

							$model = new \Kendo\Data\DataSourceSchemaModel();

							$ticketIDField = new \Kendo\Data\DataSourceSchemaModelField('ticketID');
							$ticketIDField->type('number')
										   ->editable(false)
										   ->nullable(true);

							$componentNameField = new \Kendo\Data\DataSourceSchemaModelField('component');
							$componentNameField->type('string')
											 ->validation(array('required' => true));


							

							$deviceField = new \Kendo\Data\DataSourceSchemaModelField('device');
							$deviceField->type('string')
										  ->validation(array('required' => true));

							$severityField = new \Kendo\Data\DataSourceSchemaModelField('severity');
							$severityField->type('string')
											->validation(array('required' => true));

							$typeField = new \Kendo\Data\DataSourceSchemaModelField('type');
							$typeField->type('string')
										->validation(array('required' => true));

							$model->id('ticketID')
								->addField($ticketIDField)
								->addField($componentNameField)
								->addField($deviceField)
								->addField($severityField)
								->addField($typeField);

							$schema = new \Kendo\Data\DataSourceSchema();
							$schema->data('data')
								   ->errors('errors')
								   ->model($model)
								   ->total('total');

							$dataSource = new \Kendo\Data\DataSource();

							$dataSource->transport($transport)
									   ->batch(true)
									   ->pageSize(20)
									   ->schema($schema);

							$grid = new \Kendo\UI\Grid('grid');
							
							$ticketID = new \Kendo\UI\GridColumn();
							$ticketID->field('ticketID')
								  ->format('{0:c}')
								  ->width(120)
								  ->title('Ticket ID');

							$componentName = new \Kendo\UI\GridColumn();
							$componentName->field('component')
										->title('Component');

							$deviceName = new \Kendo\UI\GridColumn();
							$deviceName->field('device')
										->title('Device');

							$severityName = new \Kendo\UI\GridColumn();
							$severityName->field('severity')
										->title('Severity');

							$typeName = new \Kendo\UI\GridColumn();
							$typeName->field('type')
									  ->title('Type');

							$command = new \Kendo\UI\GridColumn();
							$command->addCommandItem('edit')
									->addCommandItem('destroy')
									->title('&nbsp;')
									->width(250);

							$grid->addColumn($ticketID, $componentName, $deviceName, $severityName, $typeName,$command)
								 ->dataSource($dataSource)
								 ->addToolbarItem(new \Kendo\UI\GridToolbarItem('create'))
								 ->height(550)
								 ->editable('inline')
								 ->pageable(true);

							echo $grid->render();
					?>
					
					


				
				
	</body>
</html> 