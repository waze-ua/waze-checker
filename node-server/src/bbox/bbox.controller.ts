import { Controller, Get, Param } from '@nestjs/common';
import { BboxService } from './bbox.service';
import { BboxesResponseInterface } from './types/bboxesResponse.interface';

@Controller('bboxes')
export class BboxController {
  constructor(private readonly bboxService: BboxService) {}

  @Get()
  async findAll(): Promise<BboxesResponseInterface> {
    const bboxes = await this.bboxService.findAll();

    return this.bboxService.buildResponse(bboxes);
  }

  @Get(':id')
  async findRecord(@Param('id') id: number): Promise<BboxesResponseInterface> {
    const bbox = await this.bboxService.findById(id);

    return this.bboxService.buildResponse(bbox);
  }
}
