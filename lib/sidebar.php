<?php
$current_page = basename($_SERVER['PHP_SELF']);
$in_folder = (basename(dirname($_SERVER['PHP_SELF'])) == 'school pages');
$path_prefix = $in_folder ? '' : 'school pages/';
$root_prefix = $in_folder ? '../' : '';
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="<?php echo $root_prefix; ?>index.php" class="brand-link">
    <img src="<?php echo $root_prefix; ?>dist/img/Logo.png" alt="AdminLTE Logo"
      class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">School System</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="<?php echo $root_prefix; ?>dist/img/admin_image.png" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block"><?php echo $_SESSION['fullname'] ?? 'User'; ?></a>
      </div>
    </div>

    <!-- SidebarSearch Form -->
    <div class="form-inline">
      <div class="input-group" data-widget="sidebar-search">
        <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-sidebar">
            <i class="fas fa-search fa-fw"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->


        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-copy"></i>
            <p>
              Developer
              <i class="fas fa-angle-left right"></i>
              <span class="badge badge-info right">6</span>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <!-- <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>users.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Create Users</p>
              </a>
            </li> -->
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>Users_view.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Users View</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>
              Teachers
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <!-- <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>Teachers.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Add Teachers</p>
              </a>
            </li> -->
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>View_teachers.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>View Teachers</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-user-graduate"></i>
            <p>
              Students
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>Students.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Add Students</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>View_students.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>View Students</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>Attendance.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Take Attendance</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>attendance_dashboard.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Attendance Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>View_attendance.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Attendance History</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>Promotions.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Promotions</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>Documents.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Documents</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-user-friends"></i>
            <p>
              Parents
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <!-- <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>Parents.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Add Parents</p>
              </a>
            </li> -->
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>View_parents.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>View Parents</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-graduation-cap"></i>
            <p>
              Academic
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>classes.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Classes</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>sections.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Sections</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>subjects.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Subjects</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-money-bill-wave"></i>
            <p>
              Finance
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>fee_types.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Fee Types</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>fee_payments.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Collect Fees</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>view_payments.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>View Payments</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-file-invoice"></i>
            <p>
              Exams
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>exams.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Exam List</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>exam_marks.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Marks Entry</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>view_results.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Exam Results</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>import_marks.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Import Marks (Excel)</p>
              </a>
            </li>
          </ul>
        </li>

        <li
          class="nav-item <?= ($current_page == 'school_info.php' || $current_page == 'academic_years.php') ? 'menu-open' : '' ?>">
          <a href="#"
            class="nav-link <?= ($current_page == 'school_info.php' || $current_page == 'academic_years.php') ? 'active' : '' ?>">
            <i class="nav-icon fas fa-cogs"></i>
            <p>
              Settings
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>school_info.php"
                class="nav-link <?= ($current_page == 'school_info.php') ? 'active' : '' ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>School Info</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo $path_prefix; ?>academic_years.php"
                class="nav-link <?= ($current_page == 'academic_years.php') ? 'active' : '' ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Academic Years</p>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>