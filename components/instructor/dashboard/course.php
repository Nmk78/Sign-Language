<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
  <link
    rel="stylesheet"
    as="style"
    onload="this.rel='stylesheet'"
    href="https://fonts.googleapis.com/css2?display=swap&amp;family=Lexend%3Awght%40400%3B500%3B700%3B900&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900" />

  <title>Galileo Design</title>
  <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
  /* Hide scrollbar for all browsers */
  body {
    overflow: hidden;
  }

  /* If you want to hide only the vertical scrollbar */
  body {
    overflow-x: hidden;
    overflow-y: hidden; /* or 'scroll' to keep scrolling but hide the scrollbar */
  }
</style>

</head>

<body>
  <div class="px-4 py-3">
    <label class="flex flex-col min-w-40 h-12 w-full">
      <div class="flex w-full flex-1 items-stretch rounded-xl h-full">
        <div
          class="text-[#507a95] flex border-none bg-[#e8eef3] items-center justify-center pl-4 rounded-l-xl border-r-0"
          data-icon="MagnifyingGlass"
          data-size="24px"
          data-weight="regular">
          <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
            <path
              d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"></path>
          </svg>
        </div>
        <input
          placeholder="Search assignments"
          class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0e161b] focus:outline-0 focus:ring-0 border-none bg-[#e8eef3] focus:border-none h-full placeholder:text-[#507a95] px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal"
          value="" />
      </div>
    </label>
  </div>

  <?php
  $assignments = [
    [
      'id' => 1,
      'title' => 'Assignment 1: Intro to Poetry',
      'due_date' => 'Jan 15, 2023',
      'image' => 'https://cdn.usegalileo.ai/sdxl10/5e09ef0e-da7b-4ed7-b8f3-20d6cdf07593.png',
      'details' => 'This assignment introduces the basics of poetry. Students will learn about various poetic forms, elements, and techniques. The assignment includes reading selected poems, analyzing their structure, and writing a short original poem.'
    ],
    [
      'id' => 2,
      'title' => 'Assignment 2: Analyzing Poetic Devices',
      'due_date' => 'Jan 16, 2023',
      'image' => 'https://cdn.usegalileo.ai/sdxl10/eebfac85-ca2f-4cea-b0cf-a6a8fa3e9d79.png',
      'details' => 'In this assignment, students will dive deeper into poetic devices such as metaphor, simile, alliteration, and personification. They will analyze poems to identify these devices and explain their effects.'
    ],
    [
      'id' => 3,
      'title' => 'Quiz 1: Poetry Basics',
      'due_date' => 'Jan 17, 2023',
      'image' => 'https://cdn.usegalileo.ai/sdxl10/0226e59a-585a-457d-912b-b59f87edbb7a.png',
      'details' => 'This quiz will test students\' understanding of basic poetry concepts, including poetic forms, elements, and devices covered in the previous assignments.'
    ]
  ];

  foreach ($assignments as $assignment):
  ?>
    <div class="p-4 @container assignment-container" id="assignment-<?php echo $assignment['id']; ?>">
      <div class="flex flex-col items-stretch justify-start rounded-xl @xl:flex-row @xl:items-start">
        <div
          class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-xl"
          style='background-image: url("<?php echo $assignment['image']; ?>");'></div>
        <div class="flex w-full min-w-72 grow flex-col items-stretch justify-center gap-1 py-4 @xl:px-4">
          <p class="text-[#0e161b] text-lg font-bold leading-tight tracking-[-0.015em]"><?php echo $assignment['title']; ?></p>
          <div class="flex items-end gap-3 justify-between">
            <p class="text-[#507a95] text-base font-normal leading-normal">Due <?php echo $assignment['due_date']; ?></p>
            <button
              class="view-details-btn flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-8 px-4 bg-[#1d8cd7] text-[#f8fafb] text-sm font-medium leading-normal"
              data-assignment-id="<?php echo $assignment['id']; ?>">
              <span class="truncate">View details</span>
            </button>
          </div>
        </div>
      </div>
      <div class="assignment-details hidden mt-4 p-4 bg-[#e8eef3] rounded-xl">
        <h3 class="text-[#0e161b] text-base font-bold mb-2">Assignment Details:</h3>
        <p class="text-[#507a95] text-sm"><?php echo $assignment['details']; ?></p>
      </div>
    </div>
  <?php endforeach; ?>

  <script>
    $(document).ready(function() {
      $('.view-details-btn').click(function() {
        var assignmentId = $(this).data('assignment-id');
        var container = $('#assignment-' + assignmentId);
        var detailsSection = container.find('.assignment-details');
        
        $('.assignment-details').not(detailsSection).slideUp();
        detailsSection.slideToggle();
        
        container.toggleClass('expanded');
        
        if (container.hasClass('expanded')) {
          $(this).html('<span class="truncate">Hide details</span>');
        } else {
          $(this).html('<span class="truncate">View details</span>');
        }
      });
    });
  </script>
</body>
</html>
