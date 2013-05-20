<?php include "sections/header.php"; ?>
<div class="container-narrow">
	<div class="masthead">
		<h3 class="muted">BlueRidge</h3>
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
 			<td><?php echo $todo->dueDate;?><?php if($todo->overDueBy){?> <span class="label label-important"><?php echo $todo->overDueBy;?> over</span><?php }?> </td>
 			<td><a href="<?php echo $todo->url;?>"><?php echo $todo->content;?></a></td>
 			<td><?php echo $todo->owner;?></td>
 			<td><?php echo "{$todo->project} / {$todo->list}";?></td>
 		<tr>
 		<?php }?>
 	</tbody>
 </table>
</div>
<?php include "sections/footer.php"; ?>