(function () {
  const btn = document.getElementById('btnSidebar');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  if (!btn || !sidebar || !overlay) return;

  function openSidebar() {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
  }

  function closeSidebar() {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
  }

  btn.addEventListener('click', openSidebar);
  overlay.addEventListener('click', closeSidebar);
})();

window.openEditCourse = function (id, name, desc) {
  const modal = document.getElementById('editCourseModal');
  const form = document.getElementById('editCourseForm');
  const inputName = document.getElementById('editCourseName');
  const inputDesc = document.getElementById('editCourseDesc');

  form.action = `/courses/${id}`;
  inputName.value = name ?? '';
  inputDesc.value = desc ?? '';

  modal.classList.remove('hidden');
};

window.closeEditCourse = function () {
  const modal = document.getElementById('editCourseModal');
  modal.classList.add('hidden');
};