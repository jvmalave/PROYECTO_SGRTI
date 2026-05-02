import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { RequirementService } from '../../services/requirement.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Requirement } from '../../models/requirement.model';


@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule
  ],
  templateUrl: './dashboard.component.html', // Ahora sí encontrará el archivo en la misma carpeta
  styleUrl: './dashboard.component.scss'
})
export class DashboardComponent implements OnInit {
  private requirementService = inject(RequirementService);
  public requirements = this.requirementService.requirements;

  public searchTerm = signal('');
  public filteredRequirements = computed(() => {
    const term = this.searchTerm().toLowerCase();
    const all = this.requirementService.requirements();
    if (!term) return all;
    return all.filter(req => 
      req.numero_rrti.toLowerCase().includes(term) || 
      req.requesting_unit?.unidad_solicitante.toLowerCase().includes(term)
    );
  });

  // Signal para el requerimiento seleccionado (CU-007)
  public selectedRequirement = signal<Requirement | null>(null);

  // Control de estado de edición (RN-02: Lectura por defecto)
  public isEditing = signal<boolean>(false);

  public viewRequirement(req: Requirement): void {
    this.selectedRequirement.set(req);
    this.isEditing.set(false); // Siempre abrir en modo lectura[cite: 1]
  }

  public closeDetail(): void {
    this.selectedRequirement.set(null);
    this.isEditing.set(false);
  }

  // Activar campos para modificación (CU-007 Secundario)
  public enableEdit(): void {
    this.isEditing.set(true);
  }

  // Persistir cambios (CU-007 Postcondición)
  public saveChanges(): void {
    const requirement = this.selectedRequirement();
    if (requirement) {
      // Invocamos al servicio (CU-007)
        this.requirementService.updateRequirement(requirement).subscribe({
          next: (updatedReq) => {
            console.log('Requerimiento actualizado con éxito:', updatedReq.numero_rrti);
            // Actualizamos el Signal global para que el dashboard refleje el cambio
            this.requirementService.loadRequirements(); 
            this.isEditing.set(false);
            // Aquí podrías disparar una notificación de éxito (SweetAlert o similar)
          },
          error: (err) => {
            console.error('Error al actualizar:', err);
            // Manejo de error: podrías mostrar una alerta si el RRTI ya existe (RN-01)
          }
        });
    }
  }

  ngOnInit(): void {
    this.requirementService.loadRequirements();
  }
}