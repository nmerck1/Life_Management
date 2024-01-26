function scroll_table(next_prev_num, table_scroll){
    //alert("table: " + table_scroll);
    // setup the ajax request
    var xhttp = new XMLHttpRequest();
    // get variables from inputs below:
    var current_page_num = document.getElementById(table_scroll + '_current_page_num');
    if (table_scroll == 'DetailedCat') {// one exception
      var current_cat_id = document.getElementById(table_scroll + '_current_cat_id');
    }
    if (table_scroll == 'DetailedComp') {// one exception
      var current_comp_id = document.getElementById(table_scroll + '_current_comp_id');
    }
    var user_id = document.getElementById('user_id');
    var date_search = document.getElementById('date_search');
    var show_per_page = 5;
    var scroll_div_name = table_scroll + "_scroll_div";

    var action = 'Next';
    if (next_prev_num == 0) {
      action = 'Prev';
    }

    var can_scroll = true;
    if (action == 'Prev') {
      if (current_page_num.innerHTML == 1) {
        can_scroll = false;
      }
    }

    if ( can_scroll == true ) {
      // create link to send GET variables through
      var query_string = "../ajax/scroll.ajax.php";
      query_string += "?current_num=" + current_page_num.innerHTML;
      query_string += "&user_id=" + user_id.innerHTML;
      query_string += "&action=" + action;
      query_string += "&date_search=" + date_search.innerHTML;
      query_string += "&table_scroll=" + table_scroll;
      query_string += "&show_per_page=" + show_per_page;
      if (table_scroll == 'DetailedCat') {// one exception
        query_string += "&cat_id=" + current_cat_id.innerHTML;
      }
      if (table_scroll == 'DetailedComp') {// one exception
        query_string += "&comp_id=" + current_comp_id.innerHTML;
      }

      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
         document.getElementById(scroll_div_name).innerHTML = this.responseText;
        }
      };
      xhttp.open("GET", query_string, true);
      xhttp.send();

      // when the data is returned after ajax, it redirects back to inventory
      //window.location = "../pages/manage.php";
    }
}

// this is where we scroll through each month's incomes, expenses, category spending all at once with arrow buttons //
function scroll_month(next_prev_value, current_date_search, table_scroll, secondary_tab) {
  // setup the ajax request
  var xhttp = new XMLHttpRequest();
  // get variables from inputs below:
  //var date_search = document.getElementById('date_search');
  var dateVar = new Date(date_search.innerHTML);
  //alert("date search inner HTML: " + date_search.innerHTML);

  var current_page_num = 0;
  var user_id = document.getElementById('user_id');
  var show_per_page = 0;


  var action = 'Next';
  if (next_prev_value == 0) {
    action = 'Prev';
  }
  // remove extra zeros from date since we are counting hours, minutes and seconds...
  //var date_num = dateVar.getTime();
  //var date_num_string = date_num.toString();
  //var to_remove = date_num_string.length - 3;
  //date_num_string.substring(to_remove, date_num_string.length);

  //var d1 = new Date(current_date_search);
  //var d2 = new Date();
  //var greater = d1.getTime() > d2.getTime();

  //alert("dateVar search set time: " + d1 + " current month and year: " + d2);

  var can_scroll = true;
  //if (action == 'Next') {
  //  if (greater) {  // can't scroll into the future...
  //    can_scroll = false;
  //    alert("can't scroll into future");
  //  }
  //}

  if ( can_scroll == true ) {
    // create link to send GET variables through
    var query_string = "../ajax/scroll.ajax.php";
    query_string += "?action=" + action;
    query_string += "&date_search=" + current_date_search;
    query_string += "&current_num=" + current_page_num;
    query_string += "&user_id=" + user_id.innerHTML;
    query_string += "&table_scroll=" + table_scroll;
    query_string += "&show_per_page=" + show_per_page;
    query_string += "&secondary_tab=" + secondary_tab;

    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
       document.getElementById("scroll_month_div").innerHTML = this.responseText;
      }
    };
    xhttp.open("GET", query_string, true);
    xhttp.send();

    // when the data is returned after ajax, it redirects back to inventory
    //window.location = "../pages/manage.php";
  }
}

// this method is for selecting a category button in order to show more detail about that category spending this month & year //
function select_cat(cat_id, table_scroll) {
    // setup the ajax request
    var xhttp = new XMLHttpRequest();
    // get variables from inputs below:
    var current_page_num = document.getElementById(table_scroll + '_current_page_num');
    var user_id = document.getElementById('user_id');
    var date_search = document.getElementById('date_search');
    var show_per_page = 5;
    var scroll_div_name = table_scroll + "_scroll_div";

    // reset page number too
    current_page_num.innerHTML = 0;
    // update the current set cat id to be regular class for style
    var current_cat_id = document.getElementById(table_scroll + '_current_cat_id');
    var get_current_cat_button = document.getElementById('cat_button_' + current_cat_id.innerHTML);
    var get_new_cat_button = document.getElementById('cat_button_' + cat_id);
    //alert("current cat id: " + current_cat_id.innerHTML);
    //alert("new cat id: " + cat_id);
    // remove from this previous cat button
    get_current_cat_button.className = "btn btn-primary btn-sm";
    // add to new
    get_new_cat_button.className = "btn btn-dark btn-sm";

    // create link to send GET variables through
    var query_string = "../ajax/scroll.ajax.php";
    query_string += "?current_num=" + cat_id;
    query_string += "&user_id=" + user_id.innerHTML;
    query_string += "&action=" + "SelectCategoryTable";
    query_string += "&date_search=" + date_search.innerHTML;
    query_string += "&table_scroll=" + table_scroll;
    query_string += "&show_per_page=" + show_per_page;

    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
       document.getElementById(scroll_div_name).innerHTML = this.responseText;
      }
    };
    xhttp.open("GET", query_string, true);
    xhttp.send();

    // when the data is returned after ajax, it redirects back to inventory
    //window.location = "../pages/manage.php";
}


// this method is for selecting a company button in order to show more detail about that company spending this month & year //
function select_comp(comp_id, table_scroll) {
    // setup the ajax request
    var xhttp = new XMLHttpRequest();
    // get variables from inputs below:
    var current_page_num = document.getElementById(table_scroll + '_current_page_num');
    var user_id = document.getElementById('user_id');
    var date_search = document.getElementById('date_search');
    var show_per_page = 5;
    var scroll_div_name = table_scroll + "_scroll_div";

    // reset page number too
    current_page_num.innerHTML = 0;
    // update the current set cat id to be regular class for style
    var current_comp_id = document.getElementById(table_scroll + '_current_comp_id');
    var get_current_comp_button = document.getElementById('comp_button_' + current_comp_id.innerHTML);
    var get_new_comp_button = document.getElementById('comp_button_' + comp_id);
    //alert("current cat id: " + current_cat_id.innerHTML);
    //alert("new cat id: " + cat_id);
    // remove from this previous cat button
    get_current_comp_button.className = "btn btn-primary btn-sm";
    // add to new
    get_new_comp_button.className = "btn btn-dark btn-sm";

    // create link to send GET variables through
    var query_string = "../ajax/scroll.ajax.php";
    query_string += "?current_num=" + comp_id;
    query_string += "&user_id=" + user_id.innerHTML;
    query_string += "&action=" + "SelectCompanyTable";
    query_string += "&date_search=" + date_search.innerHTML;
    query_string += "&table_scroll=" + table_scroll;
    query_string += "&show_per_page=" + show_per_page;

    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
       document.getElementById(scroll_div_name).innerHTML = this.responseText;
      }
    };
    xhttp.open("GET", query_string, true);
    xhttp.send();

    // when the data is returned after ajax, it redirects back to inventory
    //window.location = "../pages/manage.php";
}
