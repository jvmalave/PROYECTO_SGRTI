import { Injectable, signal, WritableSignal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Requirement } from '../models/requirement.model'; // Crearemos esta interfaz a continuación
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class RequirementService {
  // Signal que mantiene la lista de requerimientos (SSOT)
  public requirements: WritableSignal<Requirement[]> = signal([]);
  
  // URL base que será capturada por el proxy.conf.json
  private readonly API_URL = '/api/v1/core/requirements';

  constructor(private http: HttpClient) {}

  /**
   * Carga los datos desde el Core de Postgres a través del API Laravel
   */
  public loadRequirements(): void {
    this.http.get<any>(this.API_URL).subscribe({
      next: (response) => {
        // Accedemos a la estructura de datos que vimos en tu JSON exitoso
        this.requirements.set(response.data.data);
      },
      error: (err) => {
        console.error('Error de conexión en el Ecosistema SGRTI:', err);
      }
    });
  }

  public updateRequirement(requirement: Requirement): Observable<Requirement> {
  // Solo concatenamos el ID al final de la URL base
  return this.http.put<Requirement>(`${this.API_URL}/${requirement.id}`, requirement);
}
}