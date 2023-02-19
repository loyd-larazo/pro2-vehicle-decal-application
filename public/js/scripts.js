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

  emailjs.init("7PpgiBw_6o_GCy8J-");
  emailjs.send('service_aspsnk7', 'template_6sjn1hu', templateParams)
    .then(function(response) {
       console.log('SUCCESS!', response.status, response.text);
    }, function(error) {
       console.log('FAILED...', error);
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