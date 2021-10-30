<?php


class Navbar {

  public function show_header_nav(){
    echo '<nav class="navbar navbar-inverse">';
		  echo '<div class="container-fluid">';
		    echo '<div class="navbar-header">';
		     	echo ' <a class="navbar-brand" href="home.php">';
			        echo '<p class="bi-yin-yang"></p>';
		      echo '</a>';
            echo '<a class="navbar-brand" href="home.php">Home</a>';
            echo '<a class="navbar-brand" href="vision.php">Vision</a>';
            echo '<a class="navbar-brand" href="finances.php">Finances</a>';
            echo '<a class="navbar-brand" href="routine.php">Routine</a>';
            echo '<a class="navbar-brand" href="health_fitness.php">Health & Fitness</a>';
            echo '<a class="navbar-brand" href="style.php">Style</a>';
		  		echo '</div>';
		  	echo '</div>';
			echo '</nav>';

  }

}
