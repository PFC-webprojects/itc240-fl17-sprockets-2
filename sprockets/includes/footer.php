<!--  FOOTER STARTS HERE  -->

<?php
	date_default_timezone_set('America/Los_Angeles');
	
	$copyrightYear = date('Y'); // From one year to the next, this automatically updates the copyright date that appears in the footer.
?>
	    </div> <!-- bg-faded p-4 my-4 -->
    </div>
    <!-- /.container -->

    <footer class="bg-faded text-center py-5">
      <div class="container">
        <p class="m-0">Copyright &copy; Your Website <?=$copyrightYear?></p>
      </div>
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/popper/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

  </body>

</html>
