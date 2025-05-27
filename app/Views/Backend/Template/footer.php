<script src="/Assets/js/jquery-1.11.1.min.js"></script>
	<script src="/Assets/js/bootstrap.min.js"></script>
	<script src="/Assets/js/chart.min.js"></script>
	<script src="/Assets/js/chart-data.js"></script>
	<script src="/Assets/js/easypiechart.js"></script>
	<script src="/Assets/js/easypiechart-data.js"></script>
	<script src="/Assets/js/bootstrap-datepicker.js"></script>
	<script src="/Assets/js/bootstrap-table.js"></script>	
	<script>		
		$(window).on('resize', function () {
		  if ($(window).width() > 768) $('#sidebar-collapse').collapse('show')
		})
		$(window).on('resize', function () {
		  if ($(window).width() <= 767) $('#sidebar-collapse').collapse('hide')
		})

		$(document).ready(function() {
			// Sidebar icon synchronization with Bootstrap collapse events
			$('ul.nav.menu li.parent').each(function() {
				var $parentLi = $(this);
				var $childrenUl = $parentLi.find('ul.children').first();
				var $iconEm = $parentLi.children('a').first().find('span.icon em');

				function updateIconForElement($ulElement, $iconElement) {
					if ($ulElement.hasClass('in')) {
						$iconElement.removeClass('glyphicon-plus').addClass('glyphicon-minus');
					} else {
						$iconElement.removeClass('glyphicon-minus').addClass('glyphicon-plus');
					}
				}
				updateIconForElement($childrenUl, $iconEm); // Set initial state

				$childrenUl.on('shown.bs.collapse hidden.bs.collapse', function () {
					updateIconForElement($(this), $iconEm);
				});
			});

			// Sidebar Search functionality
			var $sidebarSearchInput = $('#sidebarSearchInput');
			var $allMenuItems = $('ul.nav.menu > li:not(.divider)');
			var $logoutLi = $('ul.nav.menu > li').has('a#sidebarLogoutLink').first();

			$sidebarSearchInput.closest('form').on('submit', function(e) { // Prevent form submission on Enter
				e.preventDefault();
			});

			$sidebarSearchInput.on('keyup', function() {
				var searchTerm = $(this).val().toLowerCase().trim();

				$logoutLi.show(); // Ensure logout link is generally visible

				if (searchTerm === "") {
					$allMenuItems.show();
					$allMenuItems.find('ul.children li').show();
					$allMenuItems.filter('.parent').each(function() {
						var $parentLi = $(this);
						var $childrenUl = $parentLi.find('ul.children').first();
						if ($parentLi.hasClass('active')) {
							$childrenUl.collapse('show');
						} else {
							$childrenUl.collapse('hide');
						}
					});
					return;
				}

				$allMenuItems.each(function() {
					var $li = $(this);
					if ($logoutLi.length && $li[0] === $logoutLi[0]) return; // Skip logout from hide logic here

					var $mainLink = $li.children('a').first();
					var mainLinkText = $mainLink.clone().children().remove().end().text().trim().toLowerCase();
					var isParent = $li.hasClass('parent');
					var mainLinkMatches = mainLinkText.includes(searchTerm);
					var hasVisibleChild = false;

					if (isParent) {
						var $childrenUl = $li.find('ul.children').first();
						$childrenUl.children('li').each(function() {
							var $childLi = $(this);
							var childLinkText = $childLi.find('a').text().trim().toLowerCase();
							if (childLinkText.includes(searchTerm)) {
								$childLi.show();
								hasVisibleChild = true;
							} else {
								$childLi.hide();
							}
						});

						if (mainLinkMatches || hasVisibleChild) {
							$li.show();
							if (hasVisibleChild) {
								$childrenUl.collapse('show');
							}
						} else {
							$li.hide();
						}
					} else { // Not a parent
						if (mainLinkMatches) {
							$li.show();
						} else {
							$li.hide();
						}
					}
				});
				$logoutLi.show(); // Ensure logout is visible after filtering
			});
		});

		// Logout confirmation
		function handleLogout(event) {
			event.preventDefault(); // Mencegah aksi default link
			if (confirm('Apakah Anda yakin ingin logout?')) {
				window.location.href = '<?= site_url('logout') ?>'; // Arahkan ke URL logout jika dikonfirmasi
			}
		}

		const sidebarLogoutLink = document.getElementById('sidebarLogoutLink');
		if (sidebarLogoutLink) {
			sidebarLogoutLink.addEventListener('click', handleLogout);
		}

		const headerLogoutLink = document.getElementById('headerLogoutLink');
		if (headerLogoutLink) {
			headerLogoutLink.addEventListener('click', handleLogout);
		}
	</script>

</body>

</html>