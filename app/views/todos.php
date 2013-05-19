<?php include "sections/header.php"; ?>
<?php //var_dump($todos);?>
<div class="container">
	<div class="masthead">
		<h3 class="muted">Blueridge</h3>
	</div>
	<hr>
	<table class="table table-hover">
 	<thead> 		
 		<tr>
 			<th>Date</th>
 			<th>Description</th>
 			<th>Owner</th>
 			<th>Project / List</th>
 		<tr> 		
 	</thead>
 	<tbody>
 		<?php foreach ($todos as $todo){?>
 		<tr>
 			<td><?php echo $todo->due_on;?><span class="label label-important"></span></td>
 			<td><a href="#<?php //echo $todo->url;?>"><?php echo $todo->content;?></a></td>
 			<td><?php echo $todo->creator->name;?></td>
 			<td>Blueridgeapp / Home Page</td>
 		<tr>
 		<?php }?>
 	</tbody>
 </table>
</div>
<?php include "sections/footer.php"; ?>