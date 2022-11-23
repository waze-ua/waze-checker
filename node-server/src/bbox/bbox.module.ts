import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { BboxController } from './bbox.controller';
import { BboxEntity } from './bbox.entity';
import { BboxService } from './bbox.service';

@Module({
  imports: [TypeOrmModule.forFeature([BboxEntity])],
  controllers: [BboxController],
  providers: [BboxService],
})
export class BboxModule {}
