
<table class="table table-hover table-condensed">
	<thead>
		<th>{{trans('group.username')}}</th>
		<th>{{trans('group.email')}}</th>
		@if($roleLearngroup == ConstRole::ADMIN || $roleLearngroup == ConstRole::GROUPADMIN)
			<th>{{trans('group.delete_user')}}</th>
		@endif
	</thead>
	<tbody>
		<tr id="dragGoal" ondragover="allowDrop(event)" ondrop="drop(event)">
			<td></td>
			<td><h4>{{trans('group.drag')}}</h4></td>
			<td></td>
		</tr>
		<!-- For loop over all members of the group -->
		<?php $count = 0 ?>
		@foreach($users as $user)
		<tr>
			<td class="<?php echo $count?>">{{ DB::table('users_metadata')->where('user_id', Sentry::findUserByLogin($user['email'])->getId())->first()->displayname }}</td>
			<td class="<?php echo $count?>">{{ $user['email'] }}</td>
			@if($roleLearngroup == ConstRole::ADMIN || $roleLearngroup == ConstRole::GROUPADMIN)
				<td class="<?php echo $count?>">
					<button id="button"
						onclick="deleteUser('{{$user['email'] }}', <?php echo $count?>);return false;"
						class="btn-danger btn-mini" title="{{trans('group.delete_user')}}">
						<i class="icon-remove icon-white"></i>
					</button>
				</td>
			@endif
		</tr>
		<?php $count++ ?>
		<script>$count++;</script>
		@endforeach
		<script>displayMembers()</script>
	</tbody>
</table>
