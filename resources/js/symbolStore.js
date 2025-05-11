// stores/symbolStore.js
import { defineStore } from 'pinia';
import axios from 'axios';

export const useSymbolStore = defineStore('symbols', {
  state: () => ({
    symbols: [],
    loading: false,
    selectedSymbol: null,
  }),
  
  getters: {
    activeSymbols: (state) => state.symbols.filter(symbol => symbol.is_active),
  },
  
  actions: {
    async fetchSymbols() {
      this.loading = true;
      try {
        const response = await axios.get('/symbols');
        this.symbols = response.data;
      } catch (error) {
        console.error('Error loading symbols:', error);
      } finally {
        this.loading = false;
      }
    },
    
    async addSymbol(symbol, name = null) {
      try {
        const response = await axios.post('/symbols', { 
          symbol,
          name 
        });
        
        // Add the new symbol to our store
        this.symbols.push(response.data);
        return response.data;
      } catch (error) {
        console.error('Error adding symbol:', error);
        throw error;
      }
    },
    
    async removeSymbol(id) {
      try {
        await axios.delete(`/symbols/${id}`);
        
        // Remove from local state
        this.symbols = this.symbols.filter(symbol => symbol.id !== id);
        
        // If the deleted symbol was selected, clear selection
        if (this.selectedSymbol && !this.symbols.some(t => t.symbol === this.selectedSymbol)) {
          this.selectedSymbol = null;
        }
      } catch (error) {
        console.error('Error removing symbol:', error);
        throw error;
      }
    },
    
    selectSymbol(symbol) {
      this.selectedSymbol = symbol;
    }
  }
});