// Get form elements
const districtInput = document.getElementById('district');
const amphoeInput = document.getElementById('amphoe');
const provinceInput = document.getElementById('province');
const zipcodeInput = document.getElementById('zipcode');
const provinceCodeInput = document.getElementById('province_code');

// Autocomplete function
const autocomplete = (input, data) => {
  let currentFocus; // Keep track of the currently focused item
  let autocompleteOptions = []; // Array to store autocomplete options
  let autocompleteSelected = false; // Flag to track if an autocomplete option is selected

  input.addEventListener('input', function () {
    const value = this.value;
    closeAllLists();
    if (!value) {
      return false;
    }
    currentFocus = -1;
    const matches = data.filter((item) => item.district.toLowerCase().startsWith(value.toLowerCase()));
    matches.slice(0, 10).forEach((match) => {
      const option = document.createElement('div');
      option.innerHTML = `<strong>${match.district}</strong> - ${match.amphoe}, ${match.province}, ${match.zipcode}`;
      option.addEventListener('click', function () {
        input.value = match.district;
        amphoeInput.value = match.amphoe;
        provinceInput.value = match.province;
        zipcodeInput.value = match.zipcode;
        provinceCodeInput.value = match.province_code;
        closeAllLists();
        autocompleteSelected = true; // Set the flag to true when an option is selected
      });
      option.classList.add('autocomplete-option');
      input.parentNode.appendChild(option);
      autocompleteOptions.push(option); // Add option to the array
    });
  });

  // Function to close all autocomplete lists
  const closeAllLists = () => {
    for (let i = 0; i < autocompleteOptions.length; i++) {
      autocompleteOptions[i].remove(); // Remove options from the DOM
    }
    autocompleteOptions = []; // Clear the array
  };

  // ... โค้ดเดิม ...

  // Close the autocomplete list when clicking outside of it or when an option is selected
  document.addEventListener('click', function (e) {
    if (!e.target.classList.contains('autocomplete-option') && e.target !== input && !autocompleteSelected) {
      closeAllLists();
    }
    autocompleteSelected = false; // Reset the flag after an option is selected
  });
};


////////////////////////////////////////


  // Function to add active class to a specific autocomplete option
  const addActive = (options) => {
    removeActive(options);
    if (currentFocus >= options.length) {
      currentFocus = 0;
    }
    if (currentFocus < 0) {
      currentFocus = options.length - 1;
    }
    options[currentFocus].classList.add('autocomplete-active');
  };

  // Function to remove active class from all autocomplete options
  const removeActive = (options) => {
    for (let i = 0; i < options.length; i++) {
      options[i].classList.remove('autocomplete-active');
    }
  };

  // Close the autocomplete list when clicking outside of it
  document.addEventListener('click', function (e) {
    closeAllLists();
  });


// Fetch data from JSON file
const fetchData = async () => {
  try {
    const res = await fetch('data.json');
    if (!res.ok) {
      throw new Error('Failed to fetch data');
    }
    const data = await res.json();

    // Sort data based on matching keyword
    const keyword = districtInput.value.toLowerCase();
    const matchedData = data.filter((item) =>
      item.district.toLowerCase().includes(keyword)
    );
    const sortedData = matchedData.sort((a, b) => {
      const aMatched = a.district.toLowerCase().includes(keyword);
      const bMatched = b.district.toLowerCase().includes(keyword);
      if (aMatched && !bMatched) {
        return -1;
      } else if (!aMatched && bMatched) {
        return 1;
      } else {
        return 0;
      }
    });

    autocomplete(districtInput, sortedData);
  } catch (error) {
    console.error(error);
  }
};

// Initialize the autocomplete functionality
fetchData();


//toggle switch ในฟอร์ม
window.onload = function() {
  var formContainer = document.getElementById("form-container");
  var switchSlider = document.getElementById("switch-slider");

  formContainer.style.display = "none";
  switchSlider.style.backgroundColor = "gray";
};

