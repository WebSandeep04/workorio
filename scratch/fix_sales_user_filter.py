import os

files = [
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\today-new.blade.php",
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\today-pending.blade.php",
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\today-completed.blade.php",
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\today-followups.blade.php",
    r"d:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views\alldata\under-process.blade.php"
]

ajax_sales_users = """
  $.ajax({
    url: "{{ route('user.sales-users') }}",
    type: "GET",
    success: function (data) {
      $('#user_id').empty().append('<option value="">All Sales Users</option>');
      $.each(data, function (index, user) {
        $('#user_id').append(`<option value="${user.id}">${user.name}</option>`);
      });
    },
    error: function () {
      $('#user_id').html('<option value="">Unable to load users</option>');
    }
  });
"""

for file_path in files:
    if not os.path.exists(file_path):
        print(f"Skipping {file_path} - not found")
        continue
    
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Add AJAX call for sales users if not present
    if "user.sales-users" not in content:
        # Find where other AJAX calls are made and append
        insert_marker = '$.ajax({'
        if insert_marker in content:
            # We want to insert it before the first $.ajax or after the last one?
            # Let's insert it inside the $(document).ready block at the bottom
            # or just after another ajax call.
            # Actually, I'll insert it at the end of the last $(document).ready function.
            marker = "$(document).ready(function() {"
            if marker in content:
                content = content.replace(marker, marker + ajax_sales_users)
            elif "$(document).ready(function () {" in content:
                content = content.replace("$(document).ready(function () {", "$(document).ready(function () {" + ajax_sales_users)
    
    # Update change event listener
    old_change = "status, #city, #state, #business_type, #lead_source, #product_type'"
    new_change = "status, #city, #state, #business_type, #lead_source, #product_type, #user_id'"
    content = content.replace(old_change, new_change)
    
    # Update filter data object
    old_filter_data = "product: $('#product_type').val()"
    new_filter_data = "product: $('#product_type').val(),\n      user_id: $('#user_id').val()"
    content = content.replace(old_filter_data, new_filter_data)
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Updated {file_path}")
