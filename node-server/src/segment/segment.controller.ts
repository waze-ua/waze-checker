import { Controller, Get, Param } from '@nestjs/common';
import { SegmentService } from './segment.service';
import { SegmentsResponseInterface } from './types/segmentsResponse.interface';

@Controller('segments')
export class SegmentController {
  constructor(private readonly segmentService: SegmentService) {}

  @Get()
  async findAll(): Promise<SegmentsResponseInterface> {
    const segments = await this.segmentService.findAll();

    return this.segmentService.buildResponse(segments);
  }

  @Get(':id')
  async findRecord(
    @Param('id') id: number,
  ): Promise<SegmentsResponseInterface> {
    const segment = await this.segmentService.findById(id);

    return this.segmentService.buildResponse(segment);
  }
}
