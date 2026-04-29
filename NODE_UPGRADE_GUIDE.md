# Node.js Upgrade Guide for DARTS

## Current Status
- **Current Node.js Version**: v18.16.0
- **Required Node.js Version**: v20.19.0+ or v22.12.0+
- **NPM Version**: 9.5.1

## Why Upgrade?
- Vite (frontend build tool) requires Node.js 20.19+ or 22.12+
- Latest security updates and performance improvements
- Better compatibility with modern JavaScript features
- Support for Tailwind CSS 4.0 native binding

## Upgrade Instructions

### Option 1: Using Node Version Manager (nvm) - RECOMMENDED

#### For macOS/Linux:
```bash
# Install nvm if not already installed
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash

# Reload shell configuration
source ~/.bashrc
# or
source ~/.zshrc

# Install Node.js 22.12.0
nvm install 22.12.0

# Use the new version
nvm use 22.12.0

# Set it as default
nvm alias default 22.12.0

# Verify installation
node --version  # Should show v22.12.0
npm --version
```

#### For Windows:
1. Download nvm-windows from: https://github.com/coreybutler/nvm-windows/releases
2. Install nvm-windows
3. Open PowerShell as Administrator and run:
```powershell
nvm install 22.12.0
nvm use 22.12.0
node --version  # Should show v22.12.0
```

### Option 2: Direct Installation

#### For Windows:
1. Go to https://nodejs.org/
2. Download LTS version (v22.12.0 or latest)
3. Run the installer
4. Follow the installation wizard
5. Verify installation by opening Command Prompt and running:
```bash
node --version
npm --version
```

#### For macOS:
Using Homebrew:
```bash
brew install node@22
brew link node@22 --force
node --version
```

#### For Linux (Ubuntu/Debian):
```bash
# Remove old Node.js
sudo apt remove nodejs

# Install Node.js from NodeSource repository
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt-get install -y nodejs

# Verify
node --version
```

## Post-Upgrade Steps

### 1. Clear npm Cache
```bash
npm cache clean --force
```

### 2. Delete node_modules and package-lock.json
```bash
# On Windows (PowerShell):
rm -r node_modules
rm package-lock.json

# On macOS/Linux:
rm -rf node_modules
rm package-lock.json
```

### 3. Reinstall Dependencies
```bash
npm install
```

### 4. Clear Laravel Configuration Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### 5. Start Vite Development Server
```bash
npm run dev
```

You should see output like:
```
VITE v7.0.7 ready in 243 ms

➜  Local:   http://localhost:5173/
➜  Press h to show help
```

### 6. In Another Terminal, Start Laravel Server
```bash
php artisan serve
```

## Troubleshooting

### Issue: "Failed to load native binding"
**Solution:**
```bash
# Clear cache and reinstall
npm cache clean --force
rm -rf node_modules package-lock.json
npm install
```

### Issue: "Port 5173 already in use"
**Solution:**
```bash
# Specify different port
npm run dev -- --port 3000
```

### Issue: "Vite still not working after upgrade"
**Solution:**
1. Verify Node.js version: `node --version` (should be 20.19+ or 22.12+)
2. Delete `node_modules` and reinstall: `npm install`
3. Clear Laravel cache: `php artisan config:clear`
4. Restart all servers

### Issue: Build process very slow
**Solution:**
- This is normal with Tailwind CSS 4.0
- Check if antivirus/firewall is interfering
- Try: `npm run build` if you don't need hot reload

## Verification Steps

After upgrade, run these commands to verify everything works:

```bash
# Check Node.js version
node --version  # Should show v22.x.x

# Check npm version
npm --version   # Should be 10.x.x or higher

# Start Vite dev server
npm run dev

# In another terminal, test Laravel
php artisan serve

# Test the application
# Open http://localhost:8000 in your browser
```

## Performance Tips

1. **For Development:**
   - Use `npm run dev` for hot module replacement
   - Vite is much faster than Webpack

2. **For Production Build:**
   ```bash
   npm run build
   ```

3. **Watch File Changes:**
   ```bash
   npm run dev
   ```

## Additional Resources

- Node.js Official: https://nodejs.org/
- NVM GitHub: https://github.com/nvm-sh/nvm
- Vite Documentation: https://vitejs.dev/
- Tailwind CSS v4: https://tailwindcss.com/docs/installation

## Support

If you encounter any issues during the upgrade, check:
1. Node.js version matches requirement (20.19+ or 22.12+)
2. npm cache is cleared
3. node_modules is completely deleted and reinstalled
4. Laravel configuration cache is cleared
5. All servers (Laravel + Vite) are running on correct ports
