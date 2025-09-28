/**
 * Funciones específicas para el dashboard administrativo
 * Incluye manejo de tablas, estadísticas y funcionalidades CRUD
 * 
 * @author Emmanuel Arenilla
 * @version 1.0
 */

class DashboardManager {
    constructor() {
        this.initDataTables();
        this.initStats();
        this.bindEvents();
    }

    /**
     * Inicializar tablas de datos
     */
    initDataTables() {
        const tables = document.querySelectorAll('.data-table');
        tables.forEach(table => {
            this.enhanceTable(table);
        });
    }

    /**
     * Mejorar funcionalidad de tabla
     */
    enhanceTable(table) {
        // Agregar funcionalidad de búsqueda
        this.addTableSearch(table);
        
        // Agregar ordenamiento
        this.addTableSort(table);
        
        // Agregar paginación
        this.addTablePagination(table);
    }

    /**
     * Agregar búsqueda a la tabla
     */
    addTableSearch(table) {
        const wrapper = table.closest('.data-table-wrapper');
        if (!wrapper) return;

        const header = wrapper.querySelector('.table-header');
        if (!header) return;

        // Crear input de búsqueda si no existe
        let searchInput = header.querySelector('.table-search');
        if (!searchInput) {
            searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.className = 'form-control table-search';
            searchInput.placeholder = 'Buscar...';
            searchInput.style.maxWidth = '250px';
            
            header.querySelector('.table-actions').prepend(searchInput);
        }

        // Funcionalidad de búsqueda
        searchInput.addEventListener('input', window.EnergyApp.debounce((e) => {
            const query = e.target.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
            
            this.updateTableInfo(table);
        }, 300));
    }

    /**
     * Agregar ordenamiento a la tabla
     */
    addTableSort(table) {
        const headers = table.querySelectorAll('thead th[data-sortable]');
        
        headers.forEach((header, index) => {
            header.style.cursor = 'pointer';
            header.classList.add('sortable');
            
            // Agregar indicador visual
            if (!header.querySelector('.sort-indicator')) {
                const indicator = document.createElement('span');
                indicator.className = 'sort-indicator';
                indicator.innerHTML = ' ↕️';
                header.appendChild(indicator);
            }
            
            header.addEventListener('click', () => {
                this.sortTable(table, index, header);
            });
        });
    }

    /**
     * Ordenar tabla
     */
    sortTable(table, columnIndex, header) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const isNumeric = header.dataset.type === 'numeric';
        const isDate = header.dataset.type === 'date';
        
        // Determinar dirección de ordenamiento
        const currentDir = header.dataset.sortDir || 'asc';
        const newDir = currentDir === 'asc' ? 'desc' : 'asc';
        
        // Limpiar otros headers
        table.querySelectorAll('th').forEach(th => {
            delete th.dataset.sortDir;
            const indicator = th.querySelector('.sort-indicator');
            if (indicator) indicator.innerHTML = ' ↕️';
        });
        
        // Establecer nueva dirección
        header.dataset.sortDir = newDir;
        const indicator = header.querySelector('.sort-indicator');
        if (indicator) {
            indicator.innerHTML = newDir === 'asc' ? ' ▲' : ' ▼';
        }
        
        // Ordenar filas
        rows.sort((a, b) => {
            let aVal = a.cells[columnIndex].textContent.trim();
            let bVal = b.cells[columnIndex].textContent.trim();
            
            if (isNumeric) {
                aVal = parseFloat(aVal) || 0;
                bVal = parseFloat(bVal) || 0;
                return newDir === 'asc' ? aVal - bVal : bVal - aVal;
            } else if (isDate) {
                aVal = new Date(aVal);
                bVal = new Date(bVal);
                return newDir === 'asc' ? aVal - bVal : bVal - aVal;
            } else {
                return newDir === 'asc' 
                    ? aVal.localeCompare(bVal)
                    : bVal.localeCompare(aVal);
            }
        });
        
        // Reordenar en el DOM
        rows.forEach(row => tbody.appendChild(row));
    }

    /**
     * Agregar paginación básica
     */
    addTablePagination(table) {
        const wrapper = table.closest('.data-table-wrapper');
        if (!wrapper) return;

        const rowsPerPage = parseInt(table.dataset.perPage) || 10;
        const rows = table.querySelectorAll('tbody tr');
        
        if (rows.length <= rowsPerPage) return;

        let currentPage = 1;
        const totalPages = Math.ceil(rows.length / rowsPerPage);

        // Crear controles de paginación
        const paginationContainer = document.createElement('div');
        paginationContainer.className = 'pagination-container d-flex justify-between items-center p-3';
        paginationContainer.innerHTML = `
            <div class="table-info">
                <span class="showing-info"></span>
            </div>
            <div class="pagination-controls">
                <button class="btn btn-sm btn-outline-secondary" id="prev-page">Anterior</button>
                <span class="page-info mx-3"></span>
                <button class="btn btn-sm btn-outline-secondary" id="next-page">Siguiente</button>
            </div>
        `;
        
        wrapper.appendChild(paginationContainer);

        const showPage = (page) => {
            rows.forEach((row, index) => {
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                row.style.display = (index >= start && index < end) ? '' : 'none';
            });
            
            this.updatePaginationInfo(paginationContainer, page, totalPages, rows.length, rowsPerPage);
        };

        // Event listeners
        paginationContainer.querySelector('#prev-page').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        });

        paginationContainer.querySelector('#next-page').addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                showPage(currentPage);
            }
        });

        // Mostrar primera página
        showPage(1);
    }

    /**
     * Actualizar información de paginación
     */
    updatePaginationInfo(container, currentPage, totalPages, totalRows, rowsPerPage) {
        const start = (currentPage - 1) * rowsPerPage + 1;
        const end = Math.min(currentPage * rowsPerPage, totalRows);
        
        container.querySelector('.showing-info').textContent = 
            `Mostrando ${start} a ${end} de ${totalRows} registros`;
        
        container.querySelector('.page-info').textContent = 
            `Página ${currentPage} de ${totalPages}`;
        
        container.querySelector('#prev-page').disabled = currentPage === 1;
        container.querySelector('#next-page').disabled = currentPage === totalPages;
    }

    /**
     * Actualizar información de tabla después de filtros
     */
    updateTableInfo(table) {
        const wrapper = table.closest('.data-table-wrapper');
        const paginationContainer = wrapper?.querySelector('.pagination-container');
        if (!paginationContainer) return;

        const visibleRows = table.querySelectorAll('tbody tr[style*="display: none"]').length;
        const totalRows = table.querySelectorAll('tbody tr').length;
        const showingRows = totalRows - visibleRows;
        
        paginationContainer.querySelector('.showing-info').textContent = 
            `Mostrando ${showingRows} de ${totalRows} registros`;
    }

    /**
     * Inicializar estadísticas
     */
    initStats() {
        this.updateStatsCards();
        this.initCharts();
    }

    /**
     * Actualizar tarjetas de estadísticas
     */
    updateStatsCards() {
        // Contar registros por estado
        const totalRegisters = document.querySelectorAll('.data-table tbody tr').length;
        const todayRegisters = this.countTodayRegisters();
        const thisMonthRegisters = this.countThisMonthRegisters();
        
        // Actualizar tarjetas si existen
        this.updateStatCard('total-registers', totalRegisters);
        this.updateStatCard('today-registers', todayRegisters);
        this.updateStatCard('month-registers', thisMonthRegisters);
    }

    /**
     * Contar registros de hoy
     */
    countTodayRegisters() {
        const today = new Date().toLocaleDateString();
        const rows = document.querySelectorAll('.data-table tbody tr');
        let count = 0;
        
        rows.forEach(row => {
            const dateCell = row.querySelector('td[data-date]');
            if (dateCell) {
                const rowDate = new Date(dateCell.dataset.date).toLocaleDateString();
                if (rowDate === today) count++;
            }
        });
        
        return count;
    }

    /**
     * Contar registros del mes actual
     */
    countThisMonthRegisters() {
        const currentMonth = new Date().getMonth();
        const currentYear = new Date().getFullYear();
        const rows = document.querySelectorAll('.data-table tbody tr');
        let count = 0;
        
        rows.forEach(row => {
            const dateCell = row.querySelector('td[data-date]');
            if (dateCell) {
                const rowDate = new Date(dateCell.dataset.date);
                if (rowDate.getMonth() === currentMonth && rowDate.getFullYear() === currentYear) {
                    count++;
                }
            }
        });
        
        return count;
    }

    /**
     * Actualizar tarjeta de estadística
     */
    updateStatCard(cardId, value) {
        const card = document.getElementById(cardId);
        if (card) {
            const valueElement = card.querySelector('.stat-value');
            if (valueElement) {
                valueElement.textContent = value;
                valueElement.dataset.count = value;
            }
        }
    }

    /**
     * Inicializar gráficos (implementación básica)
     */
    initCharts() {
        // Aquí se pueden agregar gráficos con librerías como Chart.js
        console.log('Charts initialized');
    }

    /**
     * Vincular eventos específicos del dashboard
     */
    bindEvents() {
        // Evento para eliminar registros
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-delete') || e.target.closest('.btn-delete')) {
                e.preventDefault();
                this.handleDelete(e.target.closest('.btn-delete'));
            }
        });

        // Evento para editar registros
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-edit') || e.target.closest('.btn-edit')) {
                e.preventDefault();
                this.handleEdit(e.target.closest('.btn-edit'));
            }
        });

        // Evento para exportar datos
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-export') || e.target.closest('.btn-export')) {
                e.preventDefault();
                this.handleExport(e.target.closest('.btn-export'));
            }
        });

        // Refrescar estadísticas cada 5 minutos
        setInterval(() => {
            this.updateStatsCards();
        }, 300000);
    }

    /**
     * Manejar eliminación de registro
     */
    handleDelete(button) {
        const id = button.dataset.id;
        const row = button.closest('tr');
        
        if (!id) {
            console.error('ID no encontrado para eliminar');
            return;
        }

        const message = '¿Está seguro de que desea eliminar este registro? Esta acción no se puede deshacer.';
        
        if (confirm(message)) {
            // Mostrar loading
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            button.disabled = true;
            
            // Simular petición AJAX (reemplazar con llamada real)
            this.deleteRecord(id)
                .then(() => {
                    // Animar eliminación de fila
                    row.style.transition = 'opacity 0.3s ease';
                    row.style.opacity = '0';
                    
                    setTimeout(() => {
                        row.remove();
                        this.updateStatsCards();
                        window.EnergyApp.showNotification('Registro eliminado correctamente', 'success');
                    }, 300);
                })
                .catch(error => {
                    console.error('Error al eliminar:', error);
                    button.innerHTML = '<i class="fas fa-trash"></i>';
                    button.disabled = false;
                    window.EnergyApp.showNotification('Error al eliminar el registro', 'danger');
                });
        }
    }

    /**
     * Manejar edición de registro
     */
    handleEdit(button) {
        const id = button.dataset.id;
        if (!id) {
            console.error('ID no encontrado para editar');
            return;
        }

        // Obtener datos del registro
        this.getRecord(id)
            .then(data => {
                // Mostrar modal de edición con datos
                window.EnergyApp.showModal('editModal', data);
            })
            .catch(error => {
                console.error('Error al obtener registro:', error);
                window.EnergyApp.showNotification('Error al cargar los datos', 'danger');
            });
    }

    /**
     * Manejar exportación de datos
     */
    handleExport(button) {
        const format = button.dataset.format || 'csv';
        const table = document.querySelector('.data-table');
        
        if (!table) {
            window.EnergyApp.showNotification('No hay datos para exportar', 'warning');
            return;
        }

        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exportando...';
        button.disabled = true;

        setTimeout(() => {
            if (format === 'csv') {
                this.exportToCSV(table);
            } else if (format === 'excel') {
                this.exportToExcel(table);
            }
            
            button.innerHTML = '<i class="fas fa-download"></i> Exportar';
            button.disabled = false;
        }, 1000);
    }

    /**
     * Exportar tabla a CSV
     */
    exportToCSV(table) {
        const rows = table.querySelectorAll('tr');
        const csv = [];
        
        rows.forEach(row => {
            const cols = row.querySelectorAll('td, th');
            const rowData = [];
            
            cols.forEach(col => {
                // Limpiar texto y escapar comillas
                let text = col.textContent.trim().replace(/"/g, '""');
                rowData.push(`"${text}"`);
            });
            
            csv.push(rowData.join(','));
        });
        
        this.downloadFile(csv.join('\n'), 'registros.csv', 'text/csv');
    }

    /**
     * Descargar archivo
     */
    downloadFile(content, filename, contentType) {
        const blob = new Blob([content], { type: contentType });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        window.EnergyApp.showNotification('Archivo descargado correctamente', 'success');
    }

    /**
     * Eliminar registro (simulado - reemplazar con petición real)
     */
    async deleteRecord(id) {
        // Simular petición AJAX
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                // Aquí iría la llamada real a eliminar.php
                resolve();
            }, 1000);
        });
    }

    /**
     * Obtener registro (simulado - reemplazar con petición real)
     */
    async getRecord(id) {
        // Simular petición AJAX
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                // Aquí iría la llamada real para obtener los datos
                resolve({
                    id: id,
                    name: 'Usuario Ejemplo',
                    email: 'usuario@ejemplo.com',
                    phone: '1234567890'
                });
            }, 500);
        });
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Solo inicializar en páginas de dashboard
    if (document.querySelector('.dashboard-layout')) {
        window.DashboardManager = new DashboardManager();
    }
});