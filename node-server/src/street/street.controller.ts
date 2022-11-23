import { Controller, Get, Param } from '@nestjs/common';
import { StreetService } from './street.service';
import { StreetsResponseInterface } from './types/streetsResponse.interface';

@Controller('streets')
export class StreetController {
  constructor(private readonly streetService: StreetService) {}

  @Get()
  async findAll(): Promise<StreetsResponseInterface> {
    const streets = await this.streetService.findAll();

    return this.streetService.buildResponse(streets);
  }

  @Get(':id')
  async findRecord(@Param('id') id: number): Promise<StreetsResponseInterface> {
    const street = await this.streetService.findById(id);

    return this.streetService.buildResponse(street);
  }

}
