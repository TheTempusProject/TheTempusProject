/**
 * js/default.js
 *
 * This file is for 'access anywhere' javascript.
 *
 * @version  3.0
 * @author   Joey Kimsey <Joey@thetempusproject.com>
 * @link     https://TheTempusProject.com
 * @license  https://opensource.org/licenses/MIT [MIT LICENSE]
 */

/**
 * Automaticly selects/de-selects all checkboxes associated with that field
 **/
function checkAll(ele) {
    var checkboxes = document.getElementsByTagName('input');
    if (ele.checked) {
        test = true;
    } else {
        test = false;
    }
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].type == 'checkbox') {
            if (checkboxes[i].name == ele.value) {
                checkboxes[i].checked = test;
            }
        }
    }
}

// everything else is unused at the moment
function clearText(field) {
    if (field.defaultValue == field.value) field.value = '';
    else if (field.value == '') field.value = field.defaultValue;
}

function replaceTag(box, tag) {
    var Field = document.getElementById(box);
    var val = Field.value;
    var selected = val.substring(Field.selectionStart, Field.selectionEnd);
    var before = val.substring(0, Field.selectionStart);
    var after = val.substring(Field.selectionEnd, val.length);
    Field.value = before + '[' + tag + ']' + selected + '[/' + tag + ']' + after;
}

function doubleTag(box, tag, defaultVal) {
    var Field = document.getElementById(box);
    var val = Field.value;
    var selected = val.substring(Field.selectionStart, Field.selectionEnd);
    var before = val.substring(0, Field.selectionStart);
    var after = val.substring(Field.selectionEnd, val.length);
    Field.value = before + '[' + tag + '=' + defaultVal + ']' + selected + '[/' + tag + ']' + after;
}

function insertTag(box, tag) {
    var Field = document.getElementById(box);
    var currentPos = cursorPos(Field);
    var val = Field.value;
    var before = val.substring(0, currentPos);
    var after = val.substring(currentPos, val.length);
    Field.value = before + '(' + tag + ')' + after;
}

function cursorPos(el) {
    if (el.selectionStart) {
        return el.selectionStart;
    } else if (document.selection) {
        el.focus();

        var r = document.selection.createRange();
        if (r == null) {
            return 0;
        }

        var re = el.createTextRange(),
            rc = re.duplicate();
        re.moveToBookmark(r.getBookmark());
        rc.setEndPoint('EndToStart', re);

        return rc.text.length;
    }
    return 0;
}

function updateDiv(page, div) {
    $(div).load(page + ' ' + div);
}

    $(document).ready(function () {
          $('#quote-carousel').carousel({
    pause: true,
    interval: 4000,
  });
        function loadLog() {
            var oldscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height before the request
            $.ajax({
                url: "log.html",
                cache: false,
                success: function (html) {
                    $("#chatbox").html(html); //Insert chat log into the #chatbox div
                    //Auto-scroll           
                    var newscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height after the request
                    if(newscrollHeight > oldscrollHeight){
                        $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
                    }   
                }
            });
        }
        // setInterval (loadLog, 2500);
    });