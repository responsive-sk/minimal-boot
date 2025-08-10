<script>
  import { Github, Twitter, Linkedin, Heart } from 'lucide-svelte'
  
  const currentYear = new Date().getFullYear()
</script>

<footer class="relative bg-black/20 backdrop-blur-sm border-t border-white/10">
  <div class="max-w-7xl mx-auto px-6 py-12">
    <div class="grid md:grid-cols-4 gap-8">
      <!-- Brand -->
      <div class="md:col-span-2">
        <div class="flex items-center space-x-2 mb-4">
          <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">MB</span>
          </div>
          <span class="text-white font-semibold text-lg">Minimal Boot</span>
        </div>
        <p class="text-white/70 mb-6 max-w-md">
          A modern, fast, and secure web application framework built with the latest technologies.
        </p>
        <div class="flex space-x-4">
          <a href="#" class="text-white/60 hover:text-white transition-colors duration-300">
            <Github size={20} />
          </a>
          <a href="#" class="text-white/60 hover:text-white transition-colors duration-300">
            <Twitter size={20} />
          </a>
          <a href="#" class="text-white/60 hover:text-white transition-colors duration-300">
            <Linkedin size={20} />
          </a>
        </div>
      </div>
      
      <!-- Quick Links -->
      <div>
        <h3 class="text-white font-semibold mb-4">Quick Links</h3>
        <ul class="space-y-2">
          <li><a href="/" class="text-white/70 hover:text-white transition-colors duration-300">Home</a></li>
          <li><a href="/about" class="text-white/70 hover:text-white transition-colors duration-300">About</a></li>
          <li><a href="/contact" class="text-white/70 hover:text-white transition-colors duration-300">Contact</a></li>
          <li><a href="/demo" class="text-white/70 hover:text-white transition-colors duration-300">Demo</a></li>
        </ul>
      </div>
      
      <!-- Resources -->
      <div>
        <h3 class="text-white font-semibold mb-4">Resources</h3>
        <ul class="space-y-2">
          <li><a href="#" class="text-white/70 hover:text-white transition-colors duration-300">Documentation</a></li>
          <li><a href="#" class="text-white/70 hover:text-white transition-colors duration-300">API Reference</a></li>
          <li><a href="#" class="text-white/70 hover:text-white transition-colors duration-300">Examples</a></li>
          <li><a href="#" class="text-white/70 hover:text-white transition-colors duration-300">Support</a></li>
        </ul>
      </div>
    </div>
    
    <!-- Bottom Bar -->
    <div class="border-t border-white/10 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
      <p class="text-white/60 text-sm">
        © {currentYear} Minimal Boot by responsive.sk. Made with <Heart class="inline w-4 h-4 text-red-400" /> by developers.
      </p>
      <div class="flex space-x-6 mt-4 md:mt-0">
        <a href="#" class="text-white/60 hover:text-white text-sm transition-colors duration-300">Privacy</a>
        <a href="#" class="text-white/60 hover:text-white text-sm transition-colors duration-300">Terms</a>
        <a href="#" class="text-white/60 hover:text-white text-sm transition-colors duration-300">Cookies</a>
      </div>
    </div>
  </div>
</footer>
