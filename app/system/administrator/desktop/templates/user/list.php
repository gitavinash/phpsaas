<style>
	.modern-user-list {
		--ul-bg: #ffffff;
		--ul-border: #e5e7eb;
		--ul-text: #111827;
		--ul-subtext: #6b7280;
		--ul-accent: #1d4ed8;
		margin-top: 8px;
	}

	.modern-user-list h2 {
		margin: 0 0 12px;
		font-size: 24px;
		font-weight: 700;
		color: var(--ul-text);
		letter-spacing: -0.02em;
	}

	.modern-user-list .panel {
		background: var(--ul-bg);
		border: 1px solid var(--ul-border);
		border-radius: 14px;
		box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
		padding: 16px;
	}

	.modern-user-list #table {
		width: 100%;
		border-collapse: separate;
		border-spacing: 0;
		border: 1px solid var(--ul-border);
		border-radius: 12px;
		overflow: hidden;
		background: #fff;
	}

	.modern-user-list #table thead th {
		background: #f9fafb;
		color: #6b7280;
		text-transform: uppercase;
		letter-spacing: 0.04em;
		font-size: 11px;
		font-weight: 700;
		border-bottom: 1px solid var(--ul-border);
		padding: 12px 14px;
	}

	.modern-user-list #table tbody td {
		padding: 14px;
		border-bottom: 1px solid #f1f5f9;
		color: #1f2937;
		vertical-align: middle;
	}

	.modern-user-list #table tbody tr:last-child td {
		border-bottom: 0;
	}

	.modern-user-list #table tbody tr:hover td {
		background: #fbfdff;
	}

	.user-name {
		color: #0f172a;
		font-size: 13px;
		font-weight: 600;
		text-decoration: none;
	}

	.user-name:hover {
		color: var(--ul-accent);
	}

	.role-chip {
		display: inline-flex;
		align-items: center;
		border-radius: 999px;
		padding: 4px 8px;
		border: 1px solid #d1d5db;
		background: #f3f4f6;
		color: #4b5563;
		font-size: 11px;
		font-weight: 600;
		line-height: 1;
		text-transform: capitalize;
	}

	.hover {
		margin-top: 4px;
	}

	.hover a {
		margin-right: 10px;
		font-size: 12px;
		color: #6b7280;
		text-decoration: none;
	}

	.hover a:hover {
		color: var(--ul-accent);
	}

	.empty-user-state {
		text-align: center;
		color: var(--ul-subtext);
		font-size: 13px;
		padding: 28px 16px;
	}
</style>

<div class="modern-user-list">
	<h2>All <?php echo ucwords($postType . 's'); ?></h2>
	<div class="panel">
		<table id="table">
			<thead>
				<tr>
					<th>Profile Display Name</th>
					<th style="width: 220px">Email</th>
					<th style="width: 180px">User Type</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$hasUsers = false;

				if ($dataList = mysqli_query($connection, "SELECT profile_display_name, user_id, user_email, user_type FROM itssoftworld_profile, itssoftworld_user WHERE profile_user_id=user_id AND user_parent_id='" . USER_ID . "'")) {
					if (mysqli_num_rows($dataList) != 0) {
						while ($data = mysqli_fetch_assoc($dataList)) {
							$hasUsers = true;
							extract($data) ?>
							<tr>
								<td>
									<p><a class="user-name" href="user/?type=edit&editor=true&id=<?php echo $user_id; ?>"><?php echo htmlspecialchars($profile_display_name); ?></a></p>
									<div class="hover">
										<a href="user/?type=edit&editor=true&id=<?php echo $user_id; ?>"><i class="fas fa-pencil-alt"></i> Edit</a>
										<a href="delete/?type=user&id=<?php echo $user_id; ?>"><i class="fas fa-trash"></i> Delete</a>
									</div>
								</td>
								<td><?php echo htmlspecialchars($user_email); ?></td>
								<td><span class="role-chip"><?php echo htmlspecialchars($user_type); ?></span></td>
							</tr>
				<?php
						}
					}
				}

				if (!$hasUsers) {
					echo '<tr><td colspan="3"><div class="empty-user-state">No users found.</div></td></tr>';
				}
				?>
			</tbody>
		</table>
	</div>
</div>