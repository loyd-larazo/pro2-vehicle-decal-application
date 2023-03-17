/*!
    * Start Bootstrap - SB Admin v7.0.5 (https://startbootstrap.com/template/sb-admin)
    * Copyright 2013-2022 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
    // 
// Scripts
// 

window.addEventListener('DOMContentLoaded', event => {
  // Toggle the side navigation
  const sidebarToggle = document.body.querySelector('#sidebarToggle');
  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', event => {
      event.preventDefault();
      document.body.classList.toggle('sb-sidenav-toggled');
      localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
    });
  }
});


function validateEmail(email) {
  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
    return true;
  }
  return false;
}

function showError(msg) {
  $('#jsError').html(msg).removeClass('d-none');
  $(".modal").animate({ scrollTop: 0 }, "slow");
  window.scrollTo(0, 0);
}

function hideError() {
  $('#jsError').html("").addClass('d-none');
}

function validateMobile(num) {
  if (num[0] != '0' || num[1] != '9') {
    return false;
  }

  if (num.length != 11) {
    return false;
  }

  return true;
}

function sendEmailVerify(data) {
  var templateParams = {
    to_email: data.to_email,
    logo: data.logo,
    content_heading: data.content_heading,
    content_footer: data.content_footer,
    link: data.link,
    remarks: data.remarks,
  };

  emailjs.init("W7LFxtxcUYAk0VI6Q");
  emailjs.send('service_vryhqur', 'template_4tbbbbj', templateParams)
    .then(function(response) {
       console.log('SUCCESS!', response.status, response.text);
    }, function(error) {
       console.log('FAILED...', error);
    });
}

function sendVehicleUpdate(data, cb) {
  var templateParams = {
    to_email: data.to_email,
    logo: data.logo,
    status: data.status,
    remarks: data.remarks
  };

  emailjs.init("W7LFxtxcUYAk0VI6Q");
  emailjs.send('service_vryhqur', 'template_7rpkakg', templateParams)
    .then(function(response) {
       console.log('SUCCESS!', response.status, response.text);
       cb();
    }, function(error) {
       console.log('FAILED...', error);
       cb();
    });
}

function capitalize(word) {
 return word.charAt(0).toUpperCase() + word.slice(1);
}

function saveSvg(svgEl, name) {
  svgEl.attr("xmlns", "http://www.w3.org/2000/svg");
  var svgData = svgEl.html();
  var preface = '<?xml version="1.0" standalone="no"?>\r\n';
  var svgBlob = new Blob([preface, svgData], {type:"image/svg+xml;charset=utf-8"});
  var svgUrl = URL.createObjectURL(svgBlob);
  var downloadLink = document.createElement("a");
  downloadLink.href = svgUrl;
  downloadLink.download = name;
  document.body.appendChild(downloadLink);
  downloadLink.click();
  document.body.removeChild(downloadLink);
}

function numberOnly(number, isMobile) {
  number = Math.abs(number)
  if (isMobile) {
    if (number.length > this.maxLength) {
      number = number.slice(0, this.maxLength);
    }
  }

  return number;
}

function initImagePreview() {
  $('.prev-image').click(function() {
    var imgUrl = $(this).attr('src');
    $('#previewImageBody').attr('src', imgUrl);
    $('#previewImage').modal('show');
  });
}

(function($) {
  $.fn.inputFilter = function(callback, errMsg) {
    return this.on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function(e) {
      if (callback(this.value)) {
        // Accepted value
        if (["keydown","mousedown","focusout"].indexOf(e.type) >= 0){
          $(this).removeClass("input-error");
          this.setCustomValidity("");
        }
        this.oldValue = this.value;
        this.oldSelectionStart = this.selectionStart;
        this.oldSelectionEnd = this.selectionEnd;
      } else if (this.hasOwnProperty("oldValue")) {
        // Rejected value - restore the previous one
        $(this).addClass("input-error");
        this.setCustomValidity(errMsg);
        this.reportValidity();
        this.value = this.oldValue;
        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
      } else {
        // Rejected value - nothing to restore
        this.value = "";
      }
    });
  };
}(jQuery));

function secureMobile() {
  $(".mobile-number").inputFilter(function(value) {
    return /^\d*$/.test(value);    // Allow digits only, using a RegExp
  },"Only digits allowed");

  $('.mobile-number').keypress(function (e) {
    if($(e.target).prop('value').length >= 11) {
      if(e.keyCode!=32) {
        return false
      }
    } 
  });
}

function rankChange() {
  $('#rank').change(function() {
    if ($(this).val() == 'CIV') {
      $('#civFields').removeClass('d-none');
    } else {
      $('#civFields').addClass('d-none');
    }
  });
}

function officeChange() {
  $('#office').change(function() {
    if ($(this).val() == 'others') {
      $('#officeFields').removeClass('d-none');
    } else {
      $('#officeFields').addClass('d-none');
    }
  });
}

function ownVehicleChange() {
  $('#ownVehicle').change(function() {
    if ($(this).val() == 'no') {
      $('#deedOfSaleField').removeClass('d-none');
    } else {
      $('#deedOfSaleField').addClass('d-none');
    }
  });
}