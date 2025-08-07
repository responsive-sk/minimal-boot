import React, { useState, useEffect } from 'react'
import { motion } from 'framer-motion'

const ForestNavigation = () => {
  const [scrolled, setScrolled] = useState(false)
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)

  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 50)
    }

    window.addEventListener('scroll', handleScroll)
    return () => window.removeEventListener('scroll', handleScroll)
  }, [])

  const navLinks = [
    { name: 'Forest', href: '/' },
    { name: 'Nature', href: '/about' },
    { name: 'Harmony', href: '/contact' },
  ]

  return (
    <motion.nav 
      className={`fixed w-full top-0 z-50 transition-all duration-700 ${
        scrolled 
          ? 'bg-forest-main/30 backdrop-blur-md border-b border-forest-sage/20' 
          : 'bg-transparent'
      }`}
      initial={{ y: -100 }}
      animate={{ y: 0 }}
      transition={{ duration: 1, ease: "easeOut" }}
    >
      <div className="max-w-7xl mx-auto px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          {/* Forest Logo */}
          <motion.div 
            className="flex items-center"
            whileHover={{ scale: 1.05 }}
            transition={{ duration: 0.3 }}
          >
            <a href="/" className="flex items-center space-x-3 group">
              <motion.div 
                className="text-3xl"
                animate={{ rotate: [0, 5, -5, 0] }}
                transition={{ duration: 4, repeat: Infinity, ease: "easeInOut" }}
              >
                🌲
              </motion.div>
              <span className="forest-glow font-calm text-xl font-semibold hidden sm:block">
                Forest Calm
              </span>
            </a>
          </motion.div>
          
          {/* Desktop Menu */}
          <div className="hidden md:flex items-center space-x-8">
            {navLinks.map((link, index) => (
              <motion.a
                key={link.name}
                href={link.href}
                className="nature-text hover:text-forest-light transition-colors duration-500 font-medium"
                whileHover={{ y: -2 }}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: index * 0.1 + 0.5 }}
              >
                {link.name}
              </motion.a>
            ))}
            
            {/* Theme Links - peaceful */}
            <div className="flex items-center space-x-3 text-sm">
              <span className="nature-text/60">Themes:</span>
              <motion.a 
                href="/theme/switch?theme=tailwind" 
                className="text-blue-400 hover:text-blue-300 transition-colors duration-500 px-2 py-1 rounded-lg hover:bg-forest-main/20"
                whileHover={{ scale: 1.1 }}
              >
                TW
              </motion.a>
              <motion.a 
                href="/theme/switch?theme=bootstrap" 
                className="text-purple-400 hover:text-purple-300 transition-colors duration-500 px-2 py-1 rounded-lg hover:bg-forest-main/20"
                whileHover={{ scale: 1.1 }}
              >
                BS
              </motion.a>
              <motion.a 
                href="/theme/switch?theme=svelte" 
                className="text-green-400 hover:text-green-300 transition-colors duration-500 px-2 py-1 rounded-lg hover:bg-forest-main/20"
                whileHover={{ scale: 1.1 }}
              >
                SV
              </motion.a>
              <motion.a 
                href="/theme/switch?theme=vue" 
                className="text-cyan-400 hover:text-cyan-300 transition-colors duration-500 px-2 py-1 rounded-lg hover:bg-forest-main/20"
                whileHover={{ scale: 1.1 }}
              >
                VU
              </motion.a>
              <motion.a 
                href="/theme/switch?theme=react" 
                className="text-forest-light hover:text-forest-mist transition-colors duration-500 px-2 py-1 rounded-lg bg-forest-main/30 font-semibold"
                whileHover={{ scale: 1.1 }}
              >
                🌲 RC
              </motion.a>
            </div>
            
            <motion.button 
              className="forest-button"
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.95 }}
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              transition={{ delay: 1 }}
            >
              🍃 Breathe
            </motion.button>
          </div>
          
          {/* Mobile Menu Button */}
          <div className="md:hidden">
            <motion.button
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="forest-card p-2 rounded-lg"
              whileTap={{ scale: 0.95 }}
            >
              <motion.div
                animate={{ rotate: mobileMenuOpen ? 90 : 0 }}
                transition={{ duration: 0.3 }}
              >
                🌿
              </motion.div>
            </motion.button>
          </div>
        </div>
        
        {/* Mobile Menu */}
        <motion.div
          className={`md:hidden overflow-hidden ${mobileMenuOpen ? 'block' : 'hidden'}`}
          initial={{ height: 0, opacity: 0 }}
          animate={{ 
            height: mobileMenuOpen ? 'auto' : 0, 
            opacity: mobileMenuOpen ? 1 : 0 
          }}
          transition={{ duration: 0.5, ease: "easeInOut" }}
        >
          <div className="forest-card rounded-lg mt-2 p-4 space-y-4">
            {navLinks.map((link) => (
              <a
                key={link.name}
                href={link.href}
                className="block nature-text hover:text-forest-light transition-colors duration-500"
              >
                {link.name}
              </a>
            ))}
            
            <div className="border-t border-forest-sage/30 pt-4">
              <span className="nature-text/60 text-sm">Themes:</span>
              <div className="flex flex-wrap gap-2 mt-2">
                <a href="/theme/switch?theme=tailwind" className="text-blue-400 text-sm px-2 py-1 rounded bg-forest-main/20">Tailwind</a>
                <a href="/theme/switch?theme=bootstrap" className="text-purple-400 text-sm px-2 py-1 rounded bg-forest-main/20">Bootstrap</a>
                <a href="/theme/switch?theme=svelte" className="text-green-400 text-sm px-2 py-1 rounded bg-forest-main/20">Svelte</a>
                <a href="/theme/switch?theme=vue" className="text-cyan-400 text-sm px-2 py-1 rounded bg-forest-main/20">Vue</a>
                <a href="/theme/switch?theme=react" className="text-forest-light text-sm px-2 py-1 rounded bg-forest-sage/30 font-semibold">🌲 React</a>
              </div>
            </div>
            
            <button className="forest-button w-full">🍃 Breathe Deeply</button>
          </div>
        </motion.div>
      </div>
    </motion.nav>
  )
}

export default ForestNavigation
