export interface Requirement {
  id: string; // UUID
  numero_rrti: string;
  tipo_requerimiento: string;
  anio: number;
  fecha_creacion: string;
  fase_actual: 'PL' | 'ATF' | 'GR' | 'FC'; // Tipado estricto para ciclo WATCH
  estado_interno: string;
  descripcion_detallada: string;
  is_editable: boolean; // Campo calculado que viene del Backend
  
  // Relaciones (Anidadas según tu JSON exitoso)
  requesting_unit?: RequestingUnit;
  consultants?: Consultant[];
}

/**
 * Sub-interfaz para la Unidad Solicitante (CU-012)
 */
export interface RequestingUnit {
  id: number;
  unidad_solicitante: string;
  sociedad?: string;
  sistema?: string;
  // Campos del CU-012 para el Consultor Funcional
  contacto_funcional_nom: string;
  contacto_funcional_correo: string;
  contacto_funcional_telf?: string; 
  // Campos del CU-012 para el Responsable GPGTI[cite: 1]
  contacto_gpgti_nom: string;
  contacto_gpgti_correo: string; 
}

/**
 * Sub-interfaz para los Consultores Asignados
 */
export interface Consultant {
  id: string;        // Este corresponde al UUID de la tabla users
  name: string;      // Viene de identity.users.name
  email: string;     // Viene de identity.users.email
  pivot: {
    rol_cspe: string; // El rol específico en este requerimiento
  };
}