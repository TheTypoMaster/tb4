<div class="navbar-default navbar-static-side" role="navigation">
	<div class="sidebar-collapse">
		<ul class="nav" id="side-menu">
			{{--
			<li class="sidebar-search">
				<div class="input-group custom-search-form">
					<input type="text" class="form-control" placeholder="Search...">
					<span class="input-group-btn">
						<button class="btn btn-default" type="button">
							<i class="fa fa-search"></i>
						</button>
					</span>
				</div>
				<!-- /input-group -->
			</li>
			--}}
			<li>
				<a href="{{ route('admin.dashboard.index') }}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
			</li>
			<li>
				<a href="{{ route('admin.users.index') }}"><i class="fa fa-user fa-fw"></i> Users</a>
			</li>
			<li>
				<a href="{{ route('admin.bets.index') }}"><i class="fa fa-list fa-fw"></i> Bets</a>
			</li>
			<li>
				<a href="#"><i class="fa fa-money fa-fw"></i> Payments<span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<li>
						<a href="{{ route('admin.withdrawals.index') }}">Withdrawal Requests</a>
					</li>
					<li>
						<a href="{{ route('admin.account-transactions.index') }}">Account Transactions</a>
					</li>
					<li>
						<a href="{{ route('admin.free-credit-transactions.index') }}">Free Credit Transactions</a>
					</li>
					<li>
						<a href="{{ route('admin.free-credit-management.index') }}">Free Credit Management</a>
					</li>
				</ul>				
			</li>

			<li>
                <a href="#"><i class="fa fa-trophy fa-fw"></i> Tournaments<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="{{ route('admin.tournaments.index') }}"><i class="fa fa-list fa-fw"></i> Tournament List</a>
                    </li>
                    {{--<li>--}}
                        {{--<a href="{{ route('admin.tournaments.index') }}"><i class="fa fa-plus fa-fw"></i> Create Tournament</a>--}}
                    {{--</li>--}}

                </ul>
            </li>


			<li>
				<a href="{{ route('admin.reports.index') }}"><i class="fa fa-file-text fa-fw"></i> Reports</a>
			</li>


			<li>
                <a href="#"><i class="fa fa-list fa-fw"></i> Event Management<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="{{ route('admin.competitionregions.index') }}">Regions</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.sports.index') }}">Sports</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.basecompetitions.index') }}">Base Competitions</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.competitions.index') }}">Competitions</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.events.index') }}">Events</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.teams.index') }}">Teams</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.players.index') }}">Players</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.markets.index') }}">Markets</a>
                    </li>
					<li>
						<a href="{{ route('admin.markettypes.index') }}">Market Types</a>
					</li>
                    <li>
                        <a href="{{ route('admin.selections.index') }}">Selections</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.selectionprices.index') }}">Prices</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.icons.index') }}">Icons</a>
                    </li>
                </ul>
            </li>

			<li>
				<a href="{{ route('admin.promotions.index') }}"><i class="fa fa-money fa-fw"></i> Promotions</a>
			</li>

            <li>
                <a href="{{ route('admin.settings.index') }}"><i class="fa fa-cogs fa-fw"></i> Settings</a>
            </li>

			{{--
			<li>
				<a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Charts<span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<li>
						<a href="flot.html">Flot Charts</a>
					</li>
					<li>
						<a href="morris.html">Morris.js Charts</a>
					</li>
				</ul>
				<!-- /.nav-second-level -->
			</li>
			<li>
				<a href="tables.html"><i class="fa fa-table fa-fw"></i> Tables</a>
			</li>
			<li>
				<a href="forms.html"><i class="fa fa-edit fa-fw"></i> Forms</a>
			</li>
			<li>
				<a href="#"><i class="fa fa-wrench fa-fw"></i> UI Elements<span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<li>
						<a href="panels-wells.html">Panels and Wells</a>
					</li>
					<li>
						<a href="buttons.html">Buttons</a>
					</li>
					<li>
						<a href="notifications.html">Notifications</a>
					</li>
					<li>
						<a href="typography.html">Typography</a>
					</li>
					<li>
						<a href="grid.html">Grid</a>
					</li>
				</ul>
				<!-- /.nav-second-level -->
			</li>
			<li>
				<a href="#"><i class="fa fa-sitemap fa-fw"></i> Multi-Level Dropdown<span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<li>
						<a href="#">Second Level Item</a>
					</li>
					<li>
						<a href="#">Second Level Item</a>
					</li>
					<li>
						<a href="#">Third Level <span class="fa arrow"></span></a>
						<ul class="nav nav-third-level">
							<li>
								<a href="#">Third Level Item</a>
							</li>
							<li>
								<a href="#">Third Level Item</a>
							</li>
							<li>
								<a href="#">Third Level Item</a>
							</li>
							<li>
								<a href="#">Third Level Item</a>
							</li>
						</ul>
						<!-- /.nav-third-level -->
					</li>
				</ul>
				<!-- /.nav-second-level -->
			</li>
			<li>
				<a href="#"><i class="fa fa-files-o fa-fw"></i> Sample Pages<span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<li>
						<a href="blank.html">Blank Page</a>
					</li>
					<li>
						<a href="login.html">Login Page</a>
					</li>
				</ul>
				<!-- /.nav-second-level -->
			</li>
			--}}
		</ul>
		<!-- /#side-menu -->
	</div>
	<!-- /.sidebar-collapse -->
</div>
<!-- /.navbar-static-side -->