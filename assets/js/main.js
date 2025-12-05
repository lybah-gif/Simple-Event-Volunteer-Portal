// assets/js/main.js
document.addEventListener('DOMContentLoaded', function(){
  // simple confirmation for deletes
  document.querySelectorAll('.confirm-delete').forEach(btn=>{
    btn.addEventListener('click', function(e){
      if(!confirm('Are you sure? This action cannot be undone.')){
        e.preventDefault();
      }
    });
  });
});
