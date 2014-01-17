/**
 * Created by jonasse on 09.01.14.
 */

function overlay()
{/*
  * used for long running queries, display "please wait..."
  */
    el = document.getElementById("overlay");
    el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
}

function errbox()
{/*
  * used for displaying errors in HTML (enables testing with Selenium)
  */
    elx = document.getElementById("errbox");
    elx.style.visibility = (elx.style.visibility == "visible") ? "hidden" : "visible";
}

