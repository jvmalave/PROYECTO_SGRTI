import { Component, OnInit, inject } from '@angular/core';
import { RequirementService } from './services/requirement.service';
import { RouterOutlet } from '@angular/router';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet],
  templateUrl: './app.component.html',
  styleUrl: './app.component.scss'
})
export class AppComponent implements OnInit {
  // Inyectamos el servicio con el Signal de requerimientos
  private requirementService = inject(RequirementService);
  
  // Exponemos el Signal al HTML para reactividad inmediata
  public requirements = this.requirementService.requirements;

  ngOnInit(): void {
    this.requirementService.loadRequirements();
  }
}
