import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { RoadTypeController } from './roadType.controller';
import { RoadTypeEntity } from './roadType.entity';
import { RoadTypeService } from './roadType.service';

@Module({
  imports: [TypeOrmModule.forFeature([RoadTypeEntity])],
  controllers: [RoadTypeController],
  providers: [RoadTypeService],
})
export class RoadTypeModule {}
