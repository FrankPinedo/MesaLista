var TimeSync = (function() {
  var instance;
  
  function createInstance() {
    var object = {
      serverTime: null,
      localOffset: 0,
      lastSync: null,
      isSyncing: false,
      retryCount: 0,
      maxRetries: 3,

      syncWithAPI: async function() {
        if (this.isSyncing) return false;
        
        this.isSyncing = true;
        this.updateSyncIndicator('syncing');
        
        try {
          const controller = new AbortController();
          const timeoutId = setTimeout(() => controller.abort(), 5000);
          
          const response = await fetch('https://worldtimeapi.org/api/ip', {
            signal: controller.signal
          });
          
          clearTimeout(timeoutId);
          
          if (!response.ok) throw new Error('Respuesta no OK');
          
          var data = await response.json();
          this.serverTime = new Date(data.datetime);
          this.localOffset = this.serverTime - new Date();
          this.lastSync = new Date();
          this.retryCount = 0;
          
          this.updateSyncIndicator('success');
          console.log('Hora sincronizada:', this.serverTime);
          return true;
        } catch (error) {
          console.error('Error sincronizando hora:', error);
          this.retryCount++;
          
          if (this.retryCount <= this.maxRetries) {
            console.log(`Reintentando (${this.retryCount}/${this.maxRetries})...`);
            setTimeout(() => this.syncWithAPI(), 5000 * this.retryCount);
            return false;
          }
          
          this.updateSyncIndicator('error');
          this.fallbackToLocalTime();
          return false;
        } finally {
          this.isSyncing = false;
        }
      },

      fallbackToLocalTime: function() {
        console.warn('Usando hora local como fallback');
        this.localOffset = 0;
        this.updateDisplay();
        this.showFallbackWarning();
      },

      showFallbackWarning: function() {
        var warning = document.getElementById('time-sync-warning') || 
                      document.createElement('div');
        warning.id = 'time-sync-warning';
        warning.className = 'fixed bottom-4 right-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4';
        warning.innerHTML = '<p>⚠️ No se pudo sincronizar con el servidor. Usando hora local.</p>';
        
        if (!document.getElementById('time-sync-warning')) {
          document.body.appendChild(warning);
          setTimeout(() => warning.remove(), 10000);
        }
      },

      getCurrentTime: function() {
        return new Date(Date.now() + this.localOffset);
      },

      updateDisplay: function() {
        var timeElement = document.getElementById('current-time');
        if (timeElement) {
          var time = this.getCurrentTime();
          timeElement.textContent = this.formatTime(time);
        }
      },

      formatTime: function(date) {
        return date.toLocaleTimeString('es-ES', {
          hour: '2-digit',
          minute: '2-digit',
          hour12: false
        });
      },

      updateSyncIndicator: function(status) {
        var indicator = document.getElementById('time-sync-indicator');
        if (!indicator) return;

        var statusClasses = {
          default: 'bg-gray-300',
          syncing: 'bg-yellow-400 animate-pulse',
          success: 'bg-green-400',
          error: 'bg-red-400'
        };

        indicator.className = 'h-2 w-2 rounded-full transition-colors ' + 
                            (statusClasses[status] || statusClasses.default);
      },

      init: function() {
        this.updateDisplay();
        
        this.syncWithAPI();
        
        setInterval(() => this.syncWithAPI(), 3600000);
        
        setInterval(() => this.updateDisplay(), 1000);
      }
    };
    return object;
  }

  return {
    getInstance: function() {
      if (!instance) {
        instance = createInstance();
      }
      return instance;
    }
  };
})();

document.addEventListener('DOMContentLoaded', function() {
  var timeSync = TimeSync.getInstance();
  timeSync.init();
});