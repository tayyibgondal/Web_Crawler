document.getElementById("myForm").addEventListener("submit", function (event) {
  // Prevent the default form submission
  event.preventDefault();

  var formData = new FormData(this);
  var clickedButton = document.activeElement;

  // Send POST request
  if (clickedButton.value === "search") {
    fetch("search.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (response.ok) {
          return response.json();
        } else {
          throw new Error("Network response was not ok.");
        }
      })
      .then((data) => {
        var searchResultsElem = document.querySelector(".search-results ul");

        // Clear previous search results
        searchResultsElem.innerHTML = "";

        // data is json object (like associative array)!
        // Loop through the data and create list items to display
        for (let idx in data) {
          elem = document.createElement("li");
          elem.innerText = data[idx];
          searchResultsElem.appendChild(elem);
        }

        // Show search results
        var searchElem = document.querySelector(".search-results");
        searchElem.classList.remove("hidden");
      })
      .catch((error) => {
        // Handle fetch errors or JSON parsing errors
        console.error(error);
      });
  } else if (clickedButton.value == "crawl") {
    fetch("crawlUserInput.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        // Handle response if needed (e.g., log response, update UI)
        if (response.ok) {
          return;
        } else {
          console.log("Network connection error!");
        }
      })
      .catch((error) => {
        // Handle error if needed
        console.error(error);
      });

    // Show message on the screen
    messageElem = document.querySelector(".website-crawled-message");
    messageElem.classList.remove("hidden");
  }
});
