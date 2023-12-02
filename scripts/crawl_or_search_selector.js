document.getElementById("myForm").addEventListener("submit", function (event) {
  // Prevent the default form submission
  event.preventDefault();

  // Get form data to submit
  var formData = new FormData(this); // 'this' refers to the form itself which on which event handler is invoked

  // Determine which button was clicked
  var clickedButton = document.activeElement;

  // Send POST request
  if (clickedButton.value === "search") {
    fetch("search.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        console.log(response);
        // Check if the response is successful
        if (response.ok) {
          // Parse the JSON response
          return response.json();
        } else {
          throw new Error("Network response was not ok.");
        }
      })
      .then((data) => {
        // Handle the parsed JSON data (e.g., update UI with search results)
        // Select the search results element
        var searchResultsElem = document.querySelector(".search-results ul");

        // Clear previous search results
        searchResultsElem.innerHTML = "";

        // Loop through the data and create list items to display
        data.forEach((result) => {
          var listItem = document.createElement("li");
          listItem.textContent = result.title + ": " + result.description;
          searchResultsElem.appendChild(listItem);
        });

        // Show search results
        var searchElem = document.querySelector(".search-results");
        searchElem.classList.remove("hidden");
      })
      .catch((error) => {
        // Handle fetch errors or JSON parsing errors
        console.error(error);
      });
  } else if (clickedButton.value == "crawl") {
    fetch("search.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        // Handle response if needed (e.g., log response, update UI)
        if (response.ok) {
          return;
        }
        else {
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
