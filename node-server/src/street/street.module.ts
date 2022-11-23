import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { StreetController } from './street.controller';
import { StreetEntity } from './street.entity';
import { StreetService } from './street.service';

@Module({
  imports: [TypeOrmModule.forFeature([StreetEntity])],
  controllers: [StreetController],
  providers: [StreetService],
})
export class StreetModule {}
