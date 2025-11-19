<div class="enforcer-bottom-nav" role="navigation" aria-label="Enforcer quick nav">
	<a href="{{ route('dashboard') }}" class="nav-item" title="Home">
		<i class='bx bx-home'></i>
	</a>
	<a href="{{ route('enforcer.notifications') }}" class="nav-item" title="Notifications">
		<i class='bx bx-bell'></i>
	</a>

	<a href="{{ route('add.clamping') }}" id="fabAddClamping" class="nav-item fab" title="Add">
		<i class='bx bx-plus'></i>
	</a>

	<a href="{{ route('enforcer.archives') }}" class="nav-item" title="Archives">
		<i class='bx bx-archive'></i>
	</a>
	<a href="{{ route('enforcer.profile') }}" class="nav-item" title="Profile">
		<i class='bx bx-user'></i>
	</a>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function () {
		const fab = document.getElementById('fabAddClamping');
		if (!fab) return;
		fab.addEventListener('click', function (e) {
			e.preventDefault();
			// fallback: navigate to add page if exists
			if (window.location.origin) {
				window.location.href = '/add-clamping';
			}
		});
	});
</script>
