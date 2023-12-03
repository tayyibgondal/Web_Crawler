document.getElementById("myForm").addEventListener("submit", function (event) {
  // Prevent the default form submission
  event.preventDefault();
  var searchResultsContainer = document.querySelector(".search-results");
  var searchResultsElem = document.querySelector("ul");
  var messageElem = document.querySelector(".website-crawled-message");

  var formData = new FormData(this);
  var clickedButton = document.activeElement;

  // Send POST request
  if (clickedButton.value === "search") {
    console.log("Before search");
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
        console.log("After search");
        searchResultsElem.innerHTML = ""; // Clear previous search results of ul element

        data.forEach((result) => {
          var li = document.createElement("li");
          var div = document.createElement("div");
          div.classList.add("search-result");

          var title = document.createElement("h3");
          title.textContent = result.title;

          var description = document.createElement("p");
          description.textContent = result.meta_description;

          var link = document.createElement("a");
          link.href = result.url;
          link.target = "_blank";
          link.textContent = result.url;

          div.appendChild(title);
          div.appendChild(description);
          div.appendChild(link);
          li.appendChild(div);
          searchResultsElem.appendChild(li);
        });
      })
      .catch((error) => {
        console.error(error);
      });

    // Hide message if any
    messageElem.classList.add("hidden");
    // Show the search results
    searchResultsContainer.classList.remove("hidden");
  } else if (clickedButton.value === "crawl") {
    fetch("crawlUserInput.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        // Handle response if needed (e.g., log response, update UI)
        if (response.ok) {
          console.log("Crawling successful!");
          return;
        } else {
          console.log("Network connection error!");
        }
      })
      .catch((error) => {
        // Handle error if needed
        console.error(error);
      });
    // Hide search results if any
    searchResultsContainer.classList.add("hidden");
    // Show message on the screen
    messageElem.classList.remove("hidden");
  }
});
