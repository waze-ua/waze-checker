import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { SegmentController } from './segment.controller';
import { SegmentEntity } from './segment.entity';
import { SegmentService } from './segment.service';

@Module({
  imports: [TypeOrmModule.forFeature([SegmentEntity])],
  controllers: [SegmentController],
  providers: [SegmentService],
})
export class SegmentModule {}
