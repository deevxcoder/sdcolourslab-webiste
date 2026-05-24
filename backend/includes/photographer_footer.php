<!-- DESKTOP CONTENT AREA CLOSES -->
    </div>
    <footer class="border-t border-white/5 py-4 px-8 flex justify-between items-center flex-shrink-0">
      <p class="text-zinc-600 text-xs">&copy; <?= date('Y') ?> SD Colours Photobook Lab</p>
      <a href="mailto:sdcoloursphotobooklab@gmail.com" class="text-zinc-600 hover:text-zinc-400 text-xs">Support</a>
    </footer>
  </div>
</div><!-- end desktop sidebar wrapper -->

<script>
function toggleProfile() {
  const sheet  = document.getElementById('profile-sheet');
  const panel  = document.getElementById('profile-panel');
  const isOpen = !sheet.classList.contains('pointer-events-none');
  if (isOpen) {
    panel.classList.add('translate-y-full');
    sheet.classList.add('opacity-0','pointer-events-none');
    document.body.style.overflow = '';
  } else {
    sheet.classList.remove('opacity-0','pointer-events-none');
    setTimeout(()=>panel.classList.remove('translate-y-full'),10);
    document.body.style.overflow = 'hidden';
  }
}
function stepQty(btn, delta) {
  const inp = btn.parentElement.querySelector('input[type=number]');
  inp.value = Math.max(1, Math.min(999, parseInt(inp.value||1)+delta));
}
function adjustQty(btn, delta) { stepQty(btn, delta); }
</script>
</body>
</html>
