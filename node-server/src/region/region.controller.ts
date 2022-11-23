import { Controller, Get, Param } from '@nestjs/common';
import { RegionService } from './region.service';
import { RegionsResponseInterface } from './types/regionsResponse.interface';

@Controller('regions')
export class RegionController {
  constructor(private readonly regionService: RegionService) {}

  @Get()
  async findAll(): Promise<RegionsResponseInterface> {
    const regions = await this.regionService.findAll();

    return this.regionService.buildResponse(regions);
  }

  @Get(':id')
  async findRecord(@Param('id') id: number): Promise<RegionsResponseInterface> {
    const region = await this.regionService.findById(id);

    return this.regionService.buildResponse(region);
  }
}
