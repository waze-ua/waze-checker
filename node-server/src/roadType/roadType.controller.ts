import { Controller, Get, Param } from '@nestjs/common';
import { RoadTypeService } from './roadType.service';
import { RoadTypesResponseInterface } from './types/roadTypesResponse.interface';

@Controller('road-types')
export class RoadTypeController {
  constructor(private readonly roadTypeService: RoadTypeService) {}

  @Get()
  async findAll(): Promise<RoadTypesResponseInterface> {
    const roadTypes = await this.roadTypeService.findAll();

    return this.roadTypeService.buildResponse(roadTypes);
  }

  @Get(':id')
  async findRecord(@Param('id') id: number): Promise<RoadTypesResponseInterface> {
    const roadType = await this.roadTypeService.findById(id);

    return this.roadTypeService.buildResponse(roadType);
  }

}
