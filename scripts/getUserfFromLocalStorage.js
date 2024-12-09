window.onload = function () {
  const user = JSON.parse(localStorage.getItem("user"));
  const fullName = user.first_name === "admin" ? `${user.first_name}` : `${user.first_name} ${user.last_name}`

  const fullNameContainer = document.getElementById("full-name");
  fullNameContainer.textContent = fullName
};
