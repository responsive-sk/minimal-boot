import React from 'react'
import { motion } from 'framer-motion'

const ForestFooter = () => {
  const currentYear = new Date().getFullYear()

  const forestLinks = [
    { name: '🏠 Forest Home', href: '/' },
    { name: '🌿 Nature Guide', href: '/about' },
    { name: '🍃 Contact Grove', href: '/contact' },
    { name: '🌲 Demo Trail', href: '/demo' },
  ]

  const resourceLinks = [
    { name: '📚 Wisdom Library', href: '/docs' },
    { name: '🔗 API Roots', href: '/api' },
    { name: '💻 Code Seeds', href: '/examples' },
    { name: '🛠️ Support Circle', href: '/support' },
  ]

  const socialLinks = [
    { name: 'Forest GitHub', href: 'https://github.com', icon: '🌳' },
    { name: 'Nature Network', href: 'https://twitter.com', icon: '🐦' },
    { name: 'Eco LinkedIn', href: 'https://linkedin.com', icon: '🌱' },
  ]

  return (
    <footer className="relative forest-card border-t-2 border-forest-sage/30 mt-20">
      {/* Gentle Tree Silhouettes */}
      <div className="absolute top-0 left-0 w-full h-20 overflow-hidden">
        <motion.div 
          className="flex space-x-8 text-6xl opacity-20"
          animate={{ x: [-50, 50, -50] }}
          transition={{ duration: 20, repeat: Infinity, ease: "linear" }}
        >
          🌲🌳🌲🌿🌳🌲🌿🌳🌲🌳🌲🌿🌳🌲🌿🌳
        </motion.div>
      </div>

      <div className="relative z-10 max-w-7xl mx-auto px-6 py-12">
        <div className="grid md:grid-cols-4 gap-8">
          {/* Brand Section */}
          <div className="md:col-span-2">
            <motion.div 
              className="flex items-center space-x-3 mb-6"
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              transition={{ duration: 1, ease: "easeOut" }}
              viewport={{ once: true }}
            >
              <motion.div 
                className="text-3xl"
                animate={{ 
                  rotate: [0, 10, -10, 0],
                  scale: [1, 1.1, 1]
                }}
                transition={{ 
                  duration: 6, 
                  repeat: Infinity, 
                  ease: "easeInOut" 
                }}
              >
                🌲
              </motion.div>
              <span className="forest-glow font-bold text-2xl font-nature">
                Forest Calm
              </span>
            </motion.div>
            
            <motion.p 
              className="nature-text/80 mb-6 max-w-md leading-relaxed font-calm"
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 1, delay: 0.2, ease: "easeOut" }}
              viewport={{ once: true }}
            >
              A digital sanctuary where technology meets nature's wisdom. 
              Find peace, breathe deeply, and let your mind wander through our virtual forest.
            </motion.p>
            
            <div className="flex space-x-4">
              {socialLinks.map((social, index) => (
                <motion.a
                  key={social.name}
                  href={social.href}
                  className="forest-card w-12 h-12 flex items-center justify-center rounded-xl hover:bg-forest-sage/30 transition-all duration-500"
                  whileHover={{ scale: 1.1, y: -2 }}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.5, delay: 0.4 + index * 0.1 }}
                  viewport={{ once: true }}
                  aria-label={social.name}
                >
                  <span className="text-xl">{social.icon}</span>
                </motion.a>
              ))}
            </div>
          </div>
          
          {/* Forest Navigation */}
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 1, delay: 0.3, ease: "easeOut" }}
            viewport={{ once: true }}
          >
            <h3 className="forest-glow font-semibold mb-4 text-lg font-calm">
              Forest Paths
            </h3>
            <ul className="space-y-3">
              {forestLinks.map((link, index) => (
                <motion.li 
                  key={link.name}
                  initial={{ opacity: 0, x: -20 }}
                  whileInView={{ opacity: 1, x: 0 }}
                  transition={{ duration: 0.5, delay: 0.5 + index * 0.1 }}
                  viewport={{ once: true }}
                >
                  <a
                    href={link.href}
                    className="nature-text/70 hover:text-forest-light transition-all duration-500 text-sm font-calm hover:translate-x-2 block"
                  >
                    {link.name}
                  </a>
                </motion.li>
              ))}
            </ul>
          </motion.div>
          
          {/* Nature Resources */}
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 1, delay: 0.4, ease: "easeOut" }}
            viewport={{ once: true }}
          >
            <h3 className="forest-glow font-semibold mb-4 text-lg font-calm">
              Nature Resources
            </h3>
            <ul className="space-y-3">
              {resourceLinks.map((link, index) => (
                <motion.li 
                  key={link.name}
                  initial={{ opacity: 0, x: -20 }}
                  whileInView={{ opacity: 1, x: 0 }}
                  transition={{ duration: 0.5, delay: 0.6 + index * 0.1 }}
                  viewport={{ once: true }}
                >
                  <a
                    href={link.href}
                    className="nature-text/70 hover:text-forest-light transition-all duration-500 text-sm font-calm hover:translate-x-2 block"
                  >
                    {link.name}
                  </a>
                </motion.li>
              ))}
            </ul>
          </motion.div>
        </div>
        
        {/* Bottom Bar with Breathing Animation */}
        <motion.div 
          className="border-t border-forest-sage/30 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center"
          initial={{ opacity: 0 }}
          whileInView={{ opacity: 1 }}
          transition={{ duration: 1, delay: 0.8 }}
          viewport={{ once: true }}
        >
          <motion.p 
            className="nature-text/60 text-sm font-calm"
            animate={{ opacity: [0.6, 1, 0.6] }}
            transition={{ duration: 4, repeat: Infinity, ease: "easeInOut" }}
          >
            © {currentYear} Forest Calm // Digital Nature Sanctuary // 
            <span className="text-forest-light">Breathe • Relax • Heal</span>
          </motion.p>
          
          <div className="flex space-x-6 mt-4 md:mt-0">
            {['Privacy Grove', 'Terms of Nature', 'Cookie Policy'].map((legal, index) => (
              <motion.a
                key={legal}
                href={`/${legal.toLowerCase().replace(/\s+/g, '-')}`}
                className="nature-text/60 hover:text-forest-light text-sm transition-colors duration-500 font-calm"
                whileHover={{ y: -1 }}
                initial={{ opacity: 0 }}
                whileInView={{ opacity: 1 }}
                transition={{ duration: 0.5, delay: 1 + index * 0.1 }}
                viewport={{ once: true }}
              >
                {legal}
              </motion.a>
            ))}
          </div>
        </motion.div>

        {/* Gentle Wind Animation */}
        <motion.div 
          className="absolute top-4 right-4 text-2xl opacity-30"
          animate={{ 
            x: [0, 20, -10, 0],
            rotate: [0, 5, -5, 0]
          }}
          transition={{ 
            duration: 8, 
            repeat: Infinity, 
            ease: "easeInOut" 
          }}
        >
          🍃
        </motion.div>
      </div>
    </footer>
  )
}

export default ForestFooter
