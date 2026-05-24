    </div> <!-- End of flex-grow padding container -->
    
    <!-- Minimal Footer -->
    <footer class="w-full bg-secondary/10 border-t border-white/5 py-4 px-8 mt-auto flex flex-col sm:flex-row justify-between items-center gap-2 flex-shrink-0">
      <p class="text-zinc-500 text-xs">
        &copy; <?= date('Y') ?> SD Colours Photobook Lab. All rights reserved.
      </p>
      <div class="flex gap-4">
        <a href="mailto:sdcoloursphotobooklab@gmail.com" class="text-zinc-500 hover:text-zinc-400 text-xs">Support Desk</a>
      </div>
    </footer>
  </main>

  <!-- Navigation Toggle JS -->
  <script>
    const sidebar = document.getElementById('sidebar-nav');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const closeBtn = document.getElementById('sidebar-close');
    const overlay = document.getElementById('mobile-overlay');

    if (toggleBtn) {
      toggleBtn.addEventListener('click', () => {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
      });
    }

    const closeSidebar = () => {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    };

    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
  </script>
</body>
</html>
