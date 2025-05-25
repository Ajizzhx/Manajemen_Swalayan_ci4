<script src="/Assets/js/jquery-1.11.1.min.js"></script>
	<script src="/Assets/js/bootstrap.min.js"></script>
	<script src="/Assets/js/chart.min.js"></script>
	<script src="/Assets/js/chart-data.js"></script>
	<script src="/Assets/js/easypiechart.js"></script>
	<script src="/Assets/js/easypiechart-data.js"></script>
	<script src="/Assets/js/bootstrap-datepicker.js"></script>
	<script src="/Assets/js/bootstrap-table.js"></script>
	<script>
		!function ($) {
			$(document).on("click","ul.nav li.parent > a > span.icon", function(){		  
				$(this).find('em:first').toggleClass("glyphicon-minus");	  
			}); 
			$(".sidebar span.icon").find('em:first').addClass("glyphicon-plus");
		}(window.jQuery);

		$(window).on('resize', function () {
		  if ($(window).width() > 768) $('#sidebar-collapse').collapse('show')
		})
		$(window).on('resize', function () {
		  if ($(window).width() <= 767) $('#sidebar-collapse').collapse('hide')
		})
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