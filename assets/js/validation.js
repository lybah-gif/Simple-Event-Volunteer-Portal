// assets/js/validation.js

function validateLogin() {
  const email = document.getElementById('email').value.trim();
  const pass  = document.getElementById('password').value.trim();
  if (!email || !pass) {
    alert('Email and password required.');
    return false;
  }
  return true;
}

function validateRegister() {
  const name = document.getElementById('name').value.trim();
  const email = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;
  const confirm = document.getElementById('confirm_password').value;
  if (!name || !email || !password) {
    alert('All fields are required.');
    return false;
  }
  if (password.length < 6) {
    alert('Password must be at least 6 characters.');
    return false;
  }
  if (password !== confirm) {
    alert('Passwords do not match.');
    return false;
  }
  return true;
}

function validateEventForm(){
  const title = document.getElementById('title').value.trim();
  const date = document.getElementById('date').value.trim();
  if (!title || !date) {
    alert('Title and date are required.');
    return false;
  }
  return true;
}

function validateTaskForm(){
  const title = document.getElementById('task_title').value.trim();
  const volunteer = document.getElementById('volunteer_id').value;
  if (!title || !volunteer) {
    alert('Task title and volunteer required.');
    return false;
  }
  return true;
}
