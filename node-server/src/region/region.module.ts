import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { RegionController } from './region.controller';
import { RegionEntity } from './region.entity';
import { RegionService } from './region.service';

@Module({
  imports: [TypeOrmModule.forFeature([RegionEntity])],
  controllers: [RegionController],
  providers: [RegionService]
})
export class RegionModule { }
