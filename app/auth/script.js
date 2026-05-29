
// Get form elements
const districtInput = document.getElementById('district');
const amphoeInput = document.getElementById('amphoe');
const provinceInput = document.getElementById('province');
const zipcodeInput = document.getElementById('zipcode');
const provinceCodeInput = document.getElementById('province_code');
const typeInput = document.getElementById('type');

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
    const matches = data.filter((item) =>
      item.district.toLowerCase().startsWith(value.toLowerCase())
    );
    matches.slice(0, 10).forEach((match) => {
      const option = document.createElement('div');
      option.innerHTML = `<strong>${match.district}</strong> - ${match.amphoe}, ${match.province}, ${match.zipcode}`;
      option.addEventListener('click', function () {
        input.value = match.district;
        amphoeInput.value = match.amphoe;
        provinceInput.value = match.province;
        zipcodeInput.value = match.zipcode;
        provinceCodeInput.value = match.province_code;
        typeInput.value = match.type;
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
    if (
      !e.target.classList.contains('autocomplete-option') &&
      e.target !== input &&
      !autocompleteSelected
    ) {
      closeAllLists();
    }
    autocompleteSelected = false; // Reset the flag after an option is selected
  });
};

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
// สิ้นสุด autocomplete...........................................

$(document).ready(function() {
  var current_fs, next_fs, previous_fs; // Fieldsets
  var opacity;

  $(".next").click(function() {
      current_fs = $(this).parent();
      next_fs = $(this).parent().next();

      // Update progressbar
      updateProgress(current_fs, next_fs);

      // Show next step
      showStep(next_fs, current_fs);
  });

  $(".previous").click(function() {
      current_fs = $(this).parent();
      previous_fs = $(this).parent().prev();

      // Update progressbar
      updateProgress(current_fs, previous_fs);

      // Show previous step
      showStep(previous_fs, current_fs);
  });

  function updateProgress(current_step, next_step) {
      // Change progress bar
      $("#progressbar li").eq($("fieldset").index(next_step)).addClass("active");

      // Update progress bar value
      var total_steps = $("fieldset").length;
      var current_step_index = $("fieldset").index(current_step) + 1;
      var progress = (current_step_index / total_steps) * 100;
      $(".progress-bar").css("width", progress + "%");
  }

  function showStep(stepToShow, stepToHide) {
      // Hide current step
      stepToHide.hide();
      stepToHide.removeClass("active");

      // Show next/previous step
      stepToShow.show();
      stepToShow.addClass("active");
  }
});

// เงื่อนไขกรอกบัตรประชาชน
function autoTab(obj) {
  var pattern = new String("_-____-_____-__-_"); // กำหนดรูปแบบในนี้
  var pattern_ex = new String("-"); // กำหนดสัญลักษณ์หรือเครื่องหมายที่ใช้แบ่งในนี้
  var returnText = new String("");
  var obj_l = obj.value.length;
  var obj_l2 = obj_l - 1;
  for (i = 0; i < pattern.length; i++) {
  if (obj_l2 == i && pattern.charAt(i + 1) == pattern_ex) {
      returnText += obj.value + pattern_ex;
      obj.value = returnText;
  }
  }
  if (obj_l >= pattern.length) {
  obj.value = obj.value.substr(0, pattern.length);
  }
}
